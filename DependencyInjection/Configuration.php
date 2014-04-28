<?php

namespace Swarrot\SwarrotBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('swarrot');

        $rootNode
            ->children()
                ->scalarNode('provider')
                    ->defaultValue('pecl')
                    ->isRequired()
                    ->validate()
                    ->ifNotInArray(array('pecl', 'amqp_lib'))
                        ->thenInvalid('Invalid provider "%s"')
                    ->end()
                ->end()
                ->scalarNode('default_connection')->defaultValue(null)->end()
                ->scalarNode('default_command')->defaultValue('swarrot.command.base')->cannotBeEmpty()->end()
                ->arrayNode('connections')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('host')->defaultValue('127.0.0.1')->cannotBeEmpty()->end()
                            ->integerNode('port')->defaultValue(6379)->cannotBeEmpty()->end()
                            ->scalarNode('login')->defaultValue('guest')->cannotBeEmpty()->end()
                            ->scalarNode('password')->defaultValue('guest')->end()
                            ->scalarNode('vhost')->defaultValue('/')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('consumers')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('processor')->isRequired()->end()
                            ->scalarNode('command')->defaultValue(null)->end()
                            ->scalarNode('connection')->defaultValue(null)->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('messages_types')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('connection')->defaultValue(null)->end()
                            ->scalarNode('exchange')->isRequired()->end()
                            ->scalarNode('routing_key')->defaultValue(null)->end()
                            ->arrayNode('extras')
                                ->prototype('scalar')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
