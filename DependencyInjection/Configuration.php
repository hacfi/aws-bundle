<?php

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
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->children()
                    ->enumNode('client')
                        ->values($services)
                        ->defaultValue('aws')
                    ->end()
                    ->variableNode('config')
                        ->defaultValue([])
                    ->end()
                    ->booleanNode('resolve_parameters')
                        ->defaultTrue()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
