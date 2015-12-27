<?php

namespace Swarrot\SwarrotBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    protected $knownProcessors = array(
        'ack' => 'Swarrot\Processor\Ack\AckProcessor',
        'exception_catcher' => 'Swarrot\Processor\ExceptionCatcher\ExceptionCatcherProcessor',
        'max_execution_time' => 'Swarrot\Processor\MaxExecutionTime\MaxExecutionTimeProcessor',
        'max_messages' => 'Swarrot\Processor\MaxMessages\MaxMessagesProcessor',
        'retry' => 'Swarrot\Processor\Retry\RetryProcessor',
        'signal_handler' => 'Swarrot\Processor\SignalHandler\SignalHandlerProcessor',
        'object_manager' => 'Swarrot\Processor\Doctrine\ObjectManagerProcessor',
    );

    private $debug;

    public function __construct($debug)
    {
        $this->debug = $debug;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('swarrot');

        $knownProcessors = $this->knownProcessors;

        $rootNode
            ->fixXmlConfig('connection')
            ->fixXmlConfig('consumer')
            ->fixXmlConfig('messages_type')
            ->fixXmlConfig('processor', 'processors_stack')
            ->children()
                ->scalarNode('provider')
                    ->defaultValue('pecl')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('default_connection')->defaultValue(null)->end()
                ->scalarNode('default_command')->defaultValue('swarrot.command.base')->cannotBeEmpty()->end()
                ->scalarNode('publisher_logger')->defaultValue('swarrot.logger.null')->cannotBeEmpty()->end()
                ->arrayNode('connections')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->normalizeKeys(false)
                    ->prototype('array')
                        ->children()
                            ->scalarNode('host')->defaultValue('127.0.0.1')->cannotBeEmpty()->end()
                            ->integerNode('port')->defaultValue(5672)->end()
                            ->scalarNode('login')->defaultValue('guest')->cannotBeEmpty()->end()
                            ->scalarNode('password')->defaultValue('guest')->end()
                            ->scalarNode('vhost')->defaultValue('/')->cannotBeEmpty()->end()
                            ->booleanNode('ssl')->defaultValue(false)->end()
                            ->arrayNode('ssl_options')
                                ->children()
                                    ->booleanNode('verify_peer')->end()
                                    ->scalarNode('cafile')->end()
                                    ->scalarNode('local_cert')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('consumers')
                    ->useAttributeAsKey('name')
                    ->normalizeKeys(false)
                    ->prototype('array')
                        ->fixXmlConfig('extra')
                        ->children()
                            ->scalarNode('processor')->isRequired()->end()
                            ->scalarNode('command')->defaultValue(null)->end()
                            ->scalarNode('connection')->defaultValue(null)->end()
                            ->scalarNode('queue')->defaultValue(null)->end()
                            ->arrayNode('extras')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('messages_types')
                    ->useAttributeAsKey('name')
                    ->normalizeKeys(false)
                    ->prototype('array')
                        ->fixXmlConfig('extra')
                        ->children()
                            ->scalarNode('connection')->defaultValue(null)->end()
                            ->scalarNode('exchange')->isRequired()->end()
                            ->scalarNode('routing_key')->defaultValue(null)->end()
                            ->arrayNode('extras')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('processors_stack')
                    ->beforeNormalization()
                        ->ifArray()
                        ->then(function ($v) use ($knownProcessors) {
                            foreach ($v as $key => $class) {
                                if (!array_key_exists($key, $knownProcessors)) {
                                    continue;
                                }

                                if (!isset($class) || null === $class) {
                                    $v[$key] = $knownProcessors[$key];
                                }
                            }

                            return $v;
                        })
                    ->end()
                    ->useAttributeAsKey('name')
                    ->normalizeKeys(false)
                    ->prototype('scalar')->isRequired()->end()
                ->end()
                ->booleanNode('enable_collector')->defaultValue($this->debug)->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
