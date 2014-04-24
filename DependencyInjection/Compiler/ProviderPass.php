<?php

namespace Swarrot\SwarrotBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Manipulate the service definition for the provider configuration
 *
 * @author Baptiste Clavie <clavie.b@gmail.com>
 */
class ProviderPass implements CompilerPassInterface
{
    /** {@inheritDoc} */
    public function process(ContainerBuilder $container)
    {
        if ($container->has('swarrot.channel_factory.default') || !$container->hasParameter('swarrot.config')) {
            return;
        }

        $brokers = array();

        foreach ($container->findTaggedServiceIds('swarrot.provider') as $id => $tag) {
            $brokers[isset($tag[0]['alias']) ? $tag[0]['alias'] : $id] = $id;
        }

        list($provider, $connections) = $container->getParameter('swarrot.config');

        if (!isset($brokers[$provider])) {
            throw new \InvalidArgumentException(sprintf('Invalid provider "%s"', $provider));
        }

        $broker     = $brokers[$provider];
        $definition = $container->getDefinition($broker);

        $reflection = new \ReflectionClass($definition->getClass());

        if (!$reflection->implementsInterface('Swarrot\\SwarrotBundle\\Broker\\FactoryInterface')) {
            throw new \InvalidArgumentException(sprintf('The provider "%s" is not valid', $provider));
        }

        foreach ($connections as $name => $config) {
            $definition->addMethodCall('addConnection', array(
                $name,
                $config
            ));
        }

        $container->setAlias('swarrot.channel_factory.default', $broker);
        $container->getParameterBag()->remove('swarrot.config');
    }
}
