<?php

namespace Swarrot\SwarrotBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /** @var array */
    private $knownProcessors = [
        'ack' => 'Swarrot\Processor\Ack\AckProcessor',
        'exception_catcher' => 'Swarrot\Processor\ExceptionCatcher\ExceptionCatcherProcessor',
        'max_execution_time' => 'Swarrot\Processor\MaxExecutionTime\MaxExecutionTimeProcessor',
        'max_messages' => 'Swarrot\Processor\MaxMessages\MaxMessagesProcessor',
        'retry' => 'Swarrot\Processor\Retry\RetryProcessor',
        'signal_handler' => 'Swarrot\Processor\SignalHandler\SignalHandlerProcessor',
        'object_manager' => 'Swarrot\Processor\Doctrine\ObjectManagerProcessor',
        'services_resetter' => 'Swarrot\Processor\ServicesResetter\ServicesResetterProcessor',
    ];

    private $debug;

    public function __construct(bool $debug)
    {
        $this->debug = $debug;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('swarrot');
        $rootNode = $treeBuilder->getRootNode();

        $knownProcessors = $this->knownProcessors;

        /* @phpstan-ignore-next-line */
        $rootNode
            ->beforeNormalization()
                ->always()
                ->then(function ($v) {
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

                    return $v;
                })
            ->end()
            ->fixXmlConfig('connection')
            ->fixXmlConfig('consumer')
            ->fixXmlConfig('messages_type')
            ->children()
                ->scalarNode('provider')
                    ->defaultValue('pecl')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('default_connection')->defaultValue(null)->end()
                ->scalarNode('default_command')->defaultValue('swarrot.command.base')->cannotBeEmpty()->end()
                ->scalarNode('logger')->defaultValue('logger')->cannotBeEmpty()->end()
                ->scalarNode('publisher_confirm_enable')->defaultValue(false)->end()
                ->scalarNode('publisher_confirm_timeout')->defaultValue(0)->end()
                ->arrayNode('connections')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->normalizeKeys(false)
                    ->prototype('array')
                        ->children()
                            ->scalarNode('url')->info('A URL with connection information; any parameter value parsed from this string will override explicitly set parameters')->end()
                            ->scalarNode('host')->defaultValue('127.0.0.1')->end()
                            ->integerNode('port')
                                ->beforeNormalization()
                                    ->ifString()
                                    ->then(function (string $port): int {
                                        return (int) $port;
                                    })
                                ->end()
                                ->defaultValue(5672)
                            ->end()
                            ->scalarNode('login')->defaultValue('guest')->end()
                            ->scalarNode('password')->defaultValue('guest')->end()
                            ->scalarNode('vhost')->defaultValue('/')->end()

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
                        ->fixXmlConfig('command_alias', 'command_aliases')
                        ->children()
                            ->scalarNode('processor')->isRequired()->end()
                            ->scalarNode('command')->defaultValue(null)->end()
                            ->arrayNode('command_aliases')
                                ->prototype('scalar')->end()
                            ->end()
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
                                            ->prototype('variable')->end()
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
                ->booleanNode('enable_collector')->defaultValue($this->debug)->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
