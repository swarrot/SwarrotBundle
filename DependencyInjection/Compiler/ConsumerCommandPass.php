<?php

namespace Swarrot\SwarrotBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Manipulate the service definition to define consumer commands
 *
 * @author Baptiste Clavie <clavie.b@gmail.com>
 */
class ConsumerCommandPass implements CompilerPassInterface
{
    /** {@inheritDoc} */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('swarrot.consumers')) {
            return;
        }

        $commands = array();

        foreach ($container->getParameter('swarrot.consumers') as $name => $consumer) {
            $id         = 'swarrot.command.generated.' . $name;
            $commands[] = $id;

            $container->setDefinition($id, $this->buildCommandDefinition($name, $consumer));
        }

        $container->setParameter('swarrot.commands', $commands);
        $container->getParameterBag()->remove('swarrot.consumers');
    }

    /**
     * Build the custom command for a consumer
     *
     * @param string $name     Consumer's name
     * @param array  $consumer Consumer's config
     *
     * @return DefinitionDecorator
     */
    protected function buildCommandDefinition($name, array $consumer)
    {
        $definition = new DefinitionDecorator('swarrot.command.base');

        $definition
            ->replaceArgument(0, $name)
            ->replaceArgument(1, $consumer['connection'])
            ->replaceArgument(2, new Reference($consumer['processor']))
        ;

        return $definition;
    }
}
