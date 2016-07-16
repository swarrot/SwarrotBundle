<?php

namespace Swarrot\SwarrotBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * SwarrotExtension.
 */
class SwarrotExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
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

        $commands = [];
        foreach ($config['consumers'] as $name => $consumerConfig) {
            if (null === $consumerConfig['command']) {
                $consumerConfig['command'] = $config['default_command'];
            }
            if (null === $consumerConfig['connection']) {
                $consumerConfig['connection'] = $config['default_connection'];
            }

            $commands[$name] = $this->buildCommand($container, $name, $consumerConfig, $config['processors_stack']);
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
     * {@inheritDoc}
     */
    public function getConfiguration(array $configs, ContainerBuilder $container)
    {
        return new Configuration($container->getParameter('kernel.debug'));
    }

    /**
     * {@inheritDoc}
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    /**
     * {@inheritDoc}
     */
    public function getNamespace()
    {
        return 'http://swarrot.io/schema/dic/swarrot';
    }

    /**
     * buildCommand.
     *
     * @param ContainerBuilder $container
     * @param string           $name
     * @param array            $consumerConfig
     * @param array            $processorStack
     *
     * @return string
     */
    public function buildCommand(ContainerBuilder $container, $name, array $consumerConfig, array $processorStack)
    {
        $processorConfigurators = [];
        foreach ($consumerConfig['middleware_stack'] as $middlewareStackConfig) {
            $processorConfigurators[] = new Reference(
                $this->buildCommandProcessorConfigurator($container, $name, $middlewareStackConfig)
            );
        }

        $id = 'swarrot.command.generated.'.$name;
        $container->setDefinition($id, new DefinitionDecorator($consumerConfig['command']));
        $container
            ->getDefinition($id)
            ->replaceArgument(0, $name)
            ->replaceArgument(1, $consumerConfig['connection'])
            ->replaceArgument(2, new Reference($consumerConfig['processor']))
            ->replaceArgument(3, $processorConfigurators)
            ->replaceArgument(4, $consumerConfig['extras'])
            ->replaceArgument(5, $consumerConfig['queue']);

        return $id;
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $commandName
     * @param array            $middlewareStackConfig
     *
     * @return string
     */
    private function buildCommandProcessorConfigurator(
        ContainerBuilder $container,
        $commandName,
        array $middlewareStackConfig
    ) {
        $id = 'swarrot_extra.command.generated.'.$commandName.'.'.uniqid();

        $defintion = $container->setDefinition($id, new DefinitionDecorator($middlewareStackConfig['configurator']))
            ->addMethodCall('setExtras', [$middlewareStackConfig['extras']]);

        if (!empty($middlewareStackConfig['first_arg_class'])) {
            $defintion->replaceArgument(
                0,
                $middlewareStackConfig['first_arg_class']
            );
        }

        return $id;
    }
}
