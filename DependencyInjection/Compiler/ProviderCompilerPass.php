<?php

namespace Swarrot\SwarrotBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ProviderCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->has('swarrot.factory.default') || !$container->hasParameter('swarrot.provider_config')) {
            return;
        }

        $providersIds = [];

        foreach ($container->findTaggedServiceIds('swarrot.provider_factory') as $id => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['alias'])) {
                    throw new \InvalidArgumentException(
                        sprintf('The provider\'s alias is no defined for the service "%s"', $id)
                    );
                }
                $providersIds[$tag['alias']] = $id;
            }
        }

        list($provider, $connections) = $container->getParameter('swarrot.provider_config');

        if (!isset($providersIds[$provider])) {
            throw new \InvalidArgumentException(sprintf('Invalid provider "%s"', $provider));
        }

        $id = $providersIds[$provider];
        $definition = $container->getDefinition($id);
        $className = $container->getParameterBag()->resolveValue($definition->getClass());

        $reflection = new \ReflectionClass($className);

        if (!$reflection->implementsInterface('Swarrot\\SwarrotBundle\\Broker\\FactoryInterface')) {
            throw new \InvalidArgumentException(sprintf('The provider "%s" is not valid', $provider));
        }

        foreach ($connections as $name => $connectionConfig) {
            $definition->addMethodCall('addConnection', [
                $name,
                $connectionConfig,
            ]);
        }

        $container->setAlias('swarrot.factory.default', $id);
        $container->getParameterBag()->remove('swarrot.provider_config');
    }
}
