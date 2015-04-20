<?php

namespace hacfi\AwsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

use Aws\Common\Aws;

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

        $this->awsConfig = Aws::factory()->getConfig();
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
        return new Configuration($this->getAlias(), $this->awsConfig);
    }

    /**
     * {@inheritdoc}
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        foreach ($mergedConfig as $serviceId => $config) {
            $this->createService($serviceId, $config, $container);
        }
    }

    private function createService($serviceId, $config, $container)
    {
        $serviceClass = $this->getServiceClass($config['client']);

        $definition = new Definition($serviceClass);

        if ($config['client'] !== 'aws' && is_string($config['config'])) {
            $awsClass = $this->getServiceClass('aws');
            $factoryDefinition = new Definition($awsClass);
            $factoryDefinition
                ->setFactory([$awsClass, 'factory'])
                ->setArguments([$config['config']])
            ;
            $container->setDefinition($serviceId.'_factory', $factoryDefinition);

            $definition
                ->setFactory([new Reference($serviceId.'_factory'), 'get'])
                ->setArguments([$config['client']])
            ;
        } else {
            $definition
                ->setFactory([$serviceClass, 'factory'])
                ->setArguments([$config['config']])
            ;
        }

        $container->setDefinition($serviceId, $definition);
    }

    private function getServiceClass($client)
    {
        if ($client === 'aws') {
            return 'Aws\Common\Aws';
        }

        return $this->awsConfig[$client]['class'];
    }
}
