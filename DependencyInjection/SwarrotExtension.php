<?php

namespace Swarrot\SwarrotBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;

/**
 * SwarrotExtension
 */
class SwarrotExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('swarrot.xml');

        if ('pecl' === $config['provider']) {
            $id = 'swarrot.channel_factory.pecl';
        } else {
            throw new \InvalidArgumentException('Only pecl is supported for now');
        }
        $definition = $container->getDefinition($id);

        foreach ($config['connections'] as $name => $connectionConfig) {
            $definition->addMethodCall('addConnection', array(
                $name,
                $connectionConfig
            ));
        }

        $container->setAlias('swarrot.channel_factory.default', $id);

        $commands = array();
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

        $messagesTypes = array();
        foreach ($config['messages_types'] as $name => $messageConfig) {
            if (null === $messageConfig['connection']) {
                $messageConfig['connection'] = $config['default_connection'];
            }

            $messagesTypes[$name] = $messageConfig;
        }

        $container->setParameter('swarrot.messages_types', $messagesTypes);
    }

    public function buildCommand(ContainerBuilder $container, $name, array $consumerConfig)
    {
        $id = 'swarrot.command.generated.'.$name;
        $container->setDefinition($id, new DefinitionDecorator('swarrot.command.base'));
        $container
            ->getDefinition($id)
            ->replaceArgument(0, $container->getDefinition('swarrot.channel_factory.pecl'))
            ->replaceArgument(1, new Reference($consumerConfig['processor']))
            ->replaceArgument(2, $name)
            ->replaceArgument(3, $consumerConfig['connection'])
        ;

        return $id;
    }
}
