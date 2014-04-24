<?php

namespace Swarrot\SwarrotBundle\Tests\DependencyInjection\Compiler;

use Prophecy\Argument;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;

use Swarrot\SwarrotBundle\Tests\TestCase;
use Swarrot\SwarrotBundle\DependencyInjection\Compiler\ConsumerCommandPass;

class ConsumerCommandPassTest extends TestCase
{
    public function test_it_is_initializable()
    {
        $this->assertInstanceOf(
            'Swarrot\\SwarrotBundle\\DependencyInjection\\Compiler\\ConsumerCommandPass',
            new ConsumerCommandPass()
        );
    }

    public function test_does_not_run_if_no_consumers()
    {
        $container = $this->prophet->prophesize('Symfony\\Component\\DependencyInjection\\ContainerBuilder');

        $container->hasParameter(Argument::exact('swarrot.consumers'))
            ->willReturn(false)
            ->shouldBeCalledTimes(1);

        $container->setParameter()->shouldNotBeCalled();
        $container->getParameter()->shouldNotBeCalled();
        $container->setDefinition()->shouldNotBeCalled();
        $container->getParameterBag()->shouldNotBeCalled();

        $compiler = new ConsumerCommandPass;
        $compiler->process($container->reveal());
    }

    public function test_build_commands_definition()
    {
        $container = $this->prophet->prophesize('Symfony\\Component\\DependencyInjection\\ContainerBuilder');
        $bag       = $this->prophet->prophesize('Symfony\\Component\\DependencyInjection\\ParameterBag\\ParameterBag');

        $container->hasParameter(Argument::exact('swarrot.consumers'))
            ->willReturn(true)
            ->shouldBeCalledTimes(1);

        $container->getParameter(Argument::exact('swarrot.consumers'))
            ->willReturn(array(
                'foo' => array(
                    'processor'  => 'bar',
                    'connection' => 'baz'
                )
            ))
            ->shouldBeCalledTimes(1);

        $container->setDefinition(
            Argument::exact('swarrot.command.generated.foo'),
            Argument::that(function (DefinitionDecorator $decorator) {
                if ('swarrot.command.base' !== $decorator->getParent()) {
                    return false;
                }

                if ('foo' !== $decorator->getArgument(0)) {
                    return false;
                }

                if ('baz' !== $decorator->getArgument(1)) {
                    return false;
                }

                if (!$decorator->getArgument(2) instanceof Reference || 'bar' !== (string) $decorator->getArgument(2)) {
                    return false;
                }

                return true;
            })
        )->shouldBeCalledTimes(1);

        $container->setParameter(
            Argument::exact('swarrot.commands'),
            Argument::exact(array('swarrot.command.generated.foo'))
        )->shouldBeCalledTimes(1);

        $bag->remove(Argument::exact('swarrot.consumers'))
            ->shouldBeCalledTimes(1);

        $container->getParameterBag()
            ->willReturn($bag->reveal())
            ->shouldBeCalledTimes(1);

        $compiler = new ConsumerCommandPass;
        $compiler->process($container->reveal());
    }
}
