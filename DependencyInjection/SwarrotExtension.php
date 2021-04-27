<?php

namespace Swarrot\SwarrotBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * SwarrotExtension.
 */
class SwarrotExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('swarrot.xml');

        if (null === $config['default_connection']) {
            reset($config['connections']);
            $config['default_connection'] = key($config['connections']);
        }

        $container->setAlias('swarrot.logger', $config['logger']);

        $container->setParameter('swarrot.provider_config', [$config['provider'], $config['connections']]);

        $container->setParameter('swarrot.publisher_confirm_enable', $config['publisher_confirm_enable']);
        $container->setParameter('swarrot.publisher_confirm_timeout', $config['publisher_confirm_timeout']);

        $commands = [];
        foreach ($config['consumers'] as $name => $consumerConfig) {
            if (null === $consumerConfig['command']) {
                $consumerConfig['command'] = $config['default_command'];
            }
            if (null === $consumerConfig['connection']) {
                $consumerConfig['connection'] = $config['default_connection'];
            }

            $commands[$name] = $this->buildCommand($container, $name, $consumerConfig);
        }

        $container->setParameter('swarrot.commands', $commands);

        $messagesTypes = [];
        foreach ($config['messages_types'] as $name => $messageConfig) {
            if (null === $messageConfig['connection']) {
                $messageConfig['connection'] = $config['default_connection'];
            }

            $messagesTypes[$name] = $messageConfig;
        }

        $container->setParameter('swarrot.messages_types', $messagesTypes);

        if ($config['enable_collector']) {
            $loader->load('collector.xml');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(array $configs, ContainerBuilder $container): Configuration
    {
        return new Configuration((bool) $container->getParameter('kernel.debug'));
    }

    public function buildCommand(ContainerBuilder $container, string $name, array $consumerConfig): string
    {
        $processorConfigurators = [];
        foreach ($consumerConfig['middleware_stack'] as $middlewareStackConfig) {
            $configuratorId = $this->buildCommandProcessorConfigurator($container, $name, $middlewareStackConfig);
            $processorConfigurators[$configuratorId] = new Reference($configuratorId);
        }

        $id = 'swarrot.command.generated.'.$name;
        $definition = $container->setDefinition($id, new ChildDefinition($consumerConfig['command']));
        $definition
            ->replaceArgument(1, $name)
            ->replaceArgument(2, $consumerConfig['connection'])
            ->replaceArgument(3, new Reference($consumerConfig['processor']))
            ->replaceArgument(4, $processorConfigurators)
            ->replaceArgument(5, $consumerConfig['extras'])
            ->replaceArgument(6, $consumerConfig['queue'])
            ->replaceArgument(7, $consumerConfig['command_aliases'])
            ->addTag('console.command', [
                'command' => 'swarrot:consume:'.$name,
            ])
            ->setPublic(true)
        ;

        return $id;
    }

    private function buildCommandProcessorConfigurator(ContainerBuilder $container, string $commandName, array $middlewareStackConfig): string
    {
        $hash = hash('md5', $commandName.serialize($middlewareStackConfig));
        $id = 'swarrot_extra.command.generated.'.$commandName.'.'.$hash;

        $definition = $container->setDefinition($id, new ChildDefinition($middlewareStackConfig['configurator']));
        $definition->addMethodCall('setExtras', [$middlewareStackConfig['extras']]);

        if (!empty($middlewareStackConfig['first_arg_class'])) {
            $definition->replaceArgument(0, $middlewareStackConfig['first_arg_class']);
        }

        return $id;
    }
}
