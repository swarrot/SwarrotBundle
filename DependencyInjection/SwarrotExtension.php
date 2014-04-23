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
            $definition = $container->getDefinition('swarrot.channel_factory.pecl');
        } else {
            throw new \InvalidArgumentException('Only pecl is supported for now');
        }

        foreach ($config['connections'] as $name => $connectionConfig) {
            $definition->addMethodCall('addConnection', array(
                $name,
                $connectionConfig
            ));
        }

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
    }

    public function buildCommand(ContainerBuilder $container, $name, array $consumerConfig)
    {
        $id = 'swarrot.command.generated.'.$name;
        $container->setDefinition($id, new DefinitionDecorator('swarrot.command.base'));
        $container
            ->getDefinition($id)
            ->replaceArgument(0, $container->getDefinition('swarrot.channel_factory.pecl'))
            ->replaceArgument(1, $name)
            ->replaceArgument(2, $consumerConfig['processor'])
            ->replaceArgument(3, $consumerConfig['connection'])
        ;

        return $id;
    }
}
