<?php

namespace Swarrot\SwarrotBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    protected $knownProcessors = [
        'ack' => 'Swarrot\Processor\Ack\AckProcessor',
        'exception_catcher' => 'Swarrot\Processor\ExceptionCatcher\ExceptionCatcherProcessor',
        'max_execution_time' => 'Swarrot\Processor\MaxExecutionTime\MaxExecutionTimeProcessor',
        'max_messages' => 'Swarrot\Processor\MaxMessages\MaxMessagesProcessor',
        'retry' => 'Swarrot\Processor\Retry\RetryProcessor',
        'signal_handler' => 'Swarrot\Processor\SignalHandler\SignalHandlerProcessor',
        'object_manager' => 'Swarrot\Processor\Doctrine\ObjectManagerProcessor',
    ];

    private $debug;

    public function __construct($debug)
    {
        $this->debug = $debug;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('swarrot');

        $knownProcessors = $this->knownProcessors;

        $rootNode
            ->beforeNormalization()
                ->always()
                ->then(function ($v) use ($knownProcessors) {
                    // Deal with old logger config
                    if (isset($v['publisher_logger']) && !isset($v['logger'])) {
                        $v['logger'] = $v['publisher_logger'];
                    }

                    if (!isset($v['consumers'])) {
                        $v['consumers'] = [];
                    }
                    foreach ($v['consumers'] as &$consumerConfig) {
                        if (!isset($consumerConfig['middleware_stack'])) {
                            $consumerConfig['middleware_stack'] = [];
                        }
                        if (!isset($consumerConfig['extras'])) {
                            $consumerConfig['extras'] = [];
                        }
                    }

                    // Deal with old processor_stack configuration
                    if (isset($v['processors_stack']) && count($v['processors_stack'])) {
                        @trigger_error('The processors_stack key is deprecated and should not be used anymore. Use consumer\'s `middleware_stack` instead.', E_USER_DEPRECATED);

                        $map = [ // Order matters
                            'ack' => 'swarrot.processor.ack',
                            'max_execution_time' => 'swarrot.processor.max_execution_time',
                            'max_messages' => 'swarrot.processor.max_messages',
                            'exception_catcher' => 'swarrot.processor.exception_catcher',
                            'object_manager' => 'swarrot.processor.object_manager',
                            'retry' => 'swarrot.processor.retry',
                            'signal_handler' => 'swarrot.processor.signal_handler',
                        ];

                        foreach ($map as $key => $serviceName) {
                            if (!array_key_exists($key, $v['processors_stack'])) {
                                continue;
                            }

                            foreach ($v['consumers'] as &$consumerConfig) {
                                $consumerConfig['middleware_stack'][] = [
                                    'configurator' => $serviceName,
                                    'first_arg_class' => $v['processors_stack'][$key],
                                    'extras' => $consumerConfig['extras'],
                                ];
                            }
                        }
                    }

                    return $v;
                })
            ->end()
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
                ->scalarNode('publisher_logger')
                    ->validate()
                    ->always()
                        ->then(function ($v) {
                            @trigger_error('The publisher_logger key is deprecated and should not be used anymore. Use `logger` instead.', E_USER_DEPRECATED);
                        })
                    ->end()
                ->end()
                ->scalarNode('logger')->defaultValue('logger')->cannotBeEmpty()->end()
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
                            ->scalarNode('region')->defaultNull()->end()
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
                            ->arrayNode('middleware_stack')
                                ->isRequired()
                                ->prototype('array')
                                    ->fixXmlConfig('extra')
                                    ->children()
                                        ->scalarNode('configurator')->isRequired()->end()
                                        ->scalarNode('first_arg_class')->defaultValue(null)->end()
                                        ->arrayNode('extras')
                                            ->prototype('scalar')->end()
                                        ->end()
                                    ->end()
                                ->end()
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
            ->validate()
                ->ifTrue(function ($v) {
                    if ('sqs' !== $v['provider']) {
                        return false;
                    }

                    foreach ($v['connections'] as $connection) {
                        if (null === $connection['region']) {
                            return true;
                        }
                    }

                    return false;
                })
                ->thenInvalid('If you are using the sqs provider, you need to complete the region parameter')
            ->end()
        ;

        return $treeBuilder;
    }
}
