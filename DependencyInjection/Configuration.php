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
    private $awsConfig;

    /**
     * Constructor.
     *
     * @param string $alias
     * @param array  $awsConfig
     */
    public function __construct($alias, $awsConfig)
    {
        $this->alias = $alias;
        $this->awsConfig = $awsConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $services = array_keys(
            array_filter(
                $this->awsConfig,
                function ($service) {
                    return !empty($service['class']);
                }
            )
        );

        array_unshift($services, 'aws');

        $treeBuilder
            ->root($this->alias)
            ->children()
                ->variableNode('config')
                    ->defaultValue([])
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
                            ->variableNode('config')
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
