<?php

/*
 * (c) Philipp Wahala <philipp.wahala@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace hacfi\AwsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    private $alias;

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
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $services = ['aws'];
        $availableVersions = [];

        foreach (\Aws\manifest() as $key => $service) {
            $services[] = $key;

            foreach (array_keys($service['versions']) as $version) {
                if (!in_array($version, $availableVersions)) {
                    $availableVersions[] = $version;
                }
            }
        }

        $treeBuilder
            ->root($this->alias)
            ->children()
                ->variableNode('config')
                    ->defaultValue([])
                ->end()
                ->scalarNode('region')
                    ->defaultValue('eu-central-1')
                ->end()
                ->scalarNode('version')
                    ->defaultValue('latest')
                ->end()
                ->scalarNode('default_parameters_file')
                    ->defaultNull()
                ->end()
                ->booleanNode('resolve_parameters')
                    ->defaultTrue()
                ->end()
                ->arrayNode('services')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->enumNode('client')
                                ->values($services)
                                ->defaultValue('aws')
                            ->end()
                            ->enumNode('version')
                                ->values($availableVersions)
                            ->end()
                            ->variableNode('config')
                            ->end()
                            ->scalarNode('region')
                            ->end()
                            ->scalarNode('s3_stream_wrapper')
                            ->end()
                            ->scalarNode('default_parameters_file')
                            ->end()
                            ->booleanNode('resolve_parameters')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
