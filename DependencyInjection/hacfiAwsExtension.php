<?php

/*
 * (c) Philipp Wahala <philipp.wahala@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace hacfi\AwsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class hacfiAwsExtension extends ConfigurableExtension
{
    private $alias;

    private $awsConfig;

    /**
     * Constructor.
     *
     * @param string $alias
     */
    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration($this->getAlias());
    }

    /**
     * {@inheritdoc}
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.yml');

        if ($mergedConfig['default_parameters_file']) {
            $container
                ->getDefinition('hacfi_aws.event_listener.default_parameters_listener')
                ->addMethodCall('setParametersFile', [$mergedConfig['default_parameters_file']])
            ;
        }

        $s3StreamWrappers = [];

        foreach ($mergedConfig['services'] as $serviceId => $config) {
            foreach (['config', 'region', 'resolve_parameters', 'version'] as $configKey) {
                if (!isset($config[$configKey])) {
                    $config[$configKey] = $mergedConfig[$configKey];
                }
            }

            foreach (['region', 'version'] as $configKey) {
                if (!isset($config['config'][$configKey])) {
                    $config['config'][$configKey] = $config[$configKey];
                }
            }

            $serviceClass = $this->getServiceClass($config['client']);

            $definition = new Definition($serviceClass, [$config['config']]);

            if ($config['resolve_parameters']) {
                //$definition->addMethodCall('addSubscriber', [new Reference('hacfi_aws.event_listener.resolve_parameters_listener')]);
            }

            if (isset($config['default_parameters_file']) && $config['default_parameters_file']) {
                $listenerDefinition = new Definition($container->getParameter('hacfi_aws.event_listener.default_parameters_listener.class'));
                $listenerDefinition
                    ->setPublic(false)
                    ->addMethodCall('setParametersFile', [$config['default_parameters_file']])
                ;

                $container->setDefinition($serviceId . '_listener', $listenerDefinition);

                $definition->addMethodCall('addSubscriber', [new Reference($serviceId . '_listener')]);

            } elseif ($mergedConfig['default_parameters_file']) {
                $definition->addMethodCall('addSubscriber', [new Reference('hacfi_aws.event_listener.default_parameters_listener')]);
            }

            $container->setDefinition($serviceId, $definition);

            if (isset($config['s3_stream_wrapper'])) {
                if ($config['client'] !== 's3') {
                    throw new \LogicException(sprintf('Cannot register %s client as a S3 stream wrapper', $config['client']));
                }

                $protocol = is_string($config['s3_stream_wrapper']) ? $config['s3_stream_wrapper'] : 's3';

                if (isset($s3StreamWrappers[$protocol])) {
                    throw new \Exception(sprintf('Stream wrapper protocol %s already in use', $protocol));
                }

                $s3StreamWrappers[$protocol] = $serviceId;
            }
        }

        if (!empty($s3StreamWrappers)) {
            $s3StreamWrapperRegistry = new Definition('hacfi\\AwsBundle\\S3\\StreamWrapperRegistry', [$s3StreamWrappers]);
            $s3StreamWrapperRegistry->addMethodCall('setContainer', [new Reference('service_container')]);

            $container->setDefinition('hacfi_aws.s3_stream_wrapper_registry', $s3StreamWrapperRegistry);
        }
    }

    private function getServiceClass($client)
    {
        if ($client === 'aws') {
            return 'Aws\\Sdk';
        }

        if ($this->awsConfig === null) {
            $this->awsConfig = \Aws\manifest();
        }

        $namespace = $this->awsConfig[$client]['namespace'];

        return $client = 'Aws\\' . $namespace . '\\' . $namespace . 'Client';
    }
}
