<?php

namespace Swarrot\SwarrotBundle\Tests\DependencyInjection\Compiler;

use Prophecy\Argument;

use Swarrot\SwarrotBundle\Tests\TestCase;
use Swarrot\SwarrotBundle\DependencyInjection\Compiler\ProviderPass;

class ProviderPassTest extends TestCase
{
    public function test_it_is_initializable()
    {
        $this->assertInstanceOf(
            'Swarrot\\SwarrotBundle\\DependencyInjection\\Compiler\\ProviderPass',
            new ProviderPass()
        );
    }

    public function test_should_not_run_if_already_declared()
    {
        $container = $this->prophet->prophesize('Symfony\\Component\\DependencyInjection\\ContainerBuilder');

        $container->has(Argument::exact('swarrot.channel_factory.default'))
            ->willReturn(true)
            ->shouldBeCalledTimes(1);

        $container->setAlias()->shouldNotBeCalled();
        $container->hasParameter()->shouldNotBeCalled();
        $container->getParameter()->shouldNotBeCalled();
        $container->getDefinition()->shouldNotBeCalled();
        $container->getParameterBag()->shouldNotBeCalled();
        $container->findTaggedServiceIds()->shouldNotBeCalled();

        $compiler = new ProviderPass;
        $compiler->process($container->reveal());
    }

    public function test_should_not_run_if_not_configured()
    {
        $container = $this->prophet->prophesize('Symfony\\Component\\DependencyInjection\\ContainerBuilder');

        $container->has(Argument::exact('swarrot.channel_factory.default'))
            ->willReturn(false)
            ->shouldBeCalledTimes(1);

        $container->hasParameter(Argument::exact('swarrot.config'))
            ->willReturn(false)
            ->shouldBeCalledTimes(1);

        $container->setAlias()->shouldNotBeCalled();
        $container->getParameter()->shouldNotBeCalled();
        $container->getDefinition()->shouldNotBeCalled();
        $container->getParameterBag()->shouldNotBeCalled();
        $container->findTaggedServiceIds()->shouldNotBeCalled();

        $compiler = new ProviderPass;
        $compiler->process($container->reveal());
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Invalid provider "foo"
     */
    public function test_unexistant_broker()
    {
        $container = $this->prophet->prophesize('Symfony\\Component\\DependencyInjection\\ContainerBuilder');

        $container->has(Argument::exact('swarrot.channel_factory.default'))
            ->willReturn(false)
            ->shouldBeCalledTimes(1);

        $container->hasParameter(Argument::exact('swarrot.config'))
            ->willReturn(true)
            ->shouldBeCalledTimes(1);

        $container->findTaggedServiceIds(Argument::exact('swarrot.provider'))
            ->willReturn(array(
                'foo' => array(
                    array(
                        'alias' => 'foo.bar'
                    )
                ),

                'bar' => array(
                    array()
                )
            ))->shouldBeCalledTimes(1);

        $container->getParameter(Argument::exact('swarrot.config'))
            ->willReturn(array(
                'foo',
                array()
            ))->shouldBeCalledTimes(1);

        $container->setAlias()->shouldNotBeCalled();
        $container->getDefinition()->shouldNotBeCalled();
        $container->getParameterBag()->shouldNotBeCalled();

        $compiler = new ProviderPass;
        $compiler->process($container->reveal());
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage The provider "foo.bar" is not valid
     */
    public function test_invalid_broker()
    {
        $definition = $this->prophet->prophesize('Symfony\\Component\\DependencyInjection\\Definition');
        $container  = $this->prophet->prophesize('Symfony\\Component\\DependencyInjection\\ContainerBuilder');

        $definition->getClass()
            ->willReturn('stdClass')
            ->shouldBeCalledTimes(1);

        $definition->addMethodCall()->shouldNotBeCalled();

        $container->has(Argument::exact('swarrot.channel_factory.default'))
            ->willReturn(false)
            ->shouldBeCalledTimes(1);

        $container->hasParameter(Argument::exact('swarrot.config'))
            ->willReturn(true)
            ->shouldBeCalledTimes(1);

        $container->findTaggedServiceIds(Argument::exact('swarrot.provider'))
            ->willReturn(array(
                'foo' => array(
                    array(
                        'alias' => 'foo.bar'
                    )
                ),

                'bar' => array(
                    array()
                )
            ))->shouldBeCalledTimes(1);

        $container->getParameter(Argument::exact('swarrot.config'))
            ->willReturn(array(
                'foo.bar',
                array()
            ))->shouldBeCalledTimes(1);

        $container->getDefinition(Argument::exact('foo'))
            ->willReturn($definition->reveal())
            ->shouldBeCalledTimes(1);

        $container->setAlias()->shouldNotBeCalled();
        $container->getParameterBag()->shouldNotBeCalled();

        $compiler = new ProviderPass;
        $compiler->process($container->reveal());
    }

    public function test_successful_broker()
    {
        $broker     = $this->prophet->prophesize('Swarrot\\SwarrotBundle\\Broker\\FactoryInterface');
        $definition = $this->prophet->prophesize('Symfony\\Component\\DependencyInjection\\Definition');
        $container  = $this->prophet->prophesize('Symfony\\Component\\DependencyInjection\\ContainerBuilder');
        $bag        = $this->prophet->prophesize('Symfony\\Component\\DependencyInjection\\ParameterBag\\ParameterBag');

        $definition->getClass()
            ->willReturn(get_class($broker->reveal()))
            ->shouldBeCalledTimes(1);

        $definition->addMethodCall(
            Argument::exact('addConnection'),
            Argument::exact(
                array(
                    'foo',
                    array()
                )
            )
        )->shouldBeCalledTimes(1);

        $container->has(Argument::exact('swarrot.channel_factory.default'))
            ->willReturn(false)
            ->shouldBeCalledTimes(1);

        $container->hasParameter(Argument::exact('swarrot.config'))
            ->willReturn(true)
            ->shouldBeCalledTimes(1);

        $container->findTaggedServiceIds(Argument::exact('swarrot.provider'))
            ->willReturn(array(
                'foo' => array(
                    array(
                        'alias' => 'foo.bar'
                    )
                ),

                'bar' => array(
                    array()
                )
            ))->shouldBeCalledTimes(1);

        $container->getParameter(Argument::exact('swarrot.config'))
            ->willReturn(array(
                'foo.bar',
                array(
                    'foo' => array()
                )
            ))->shouldBeCalledTimes(1);

        $container->getDefinition(Argument::exact('foo'))
            ->willReturn($definition->reveal())
            ->shouldBeCalledTimes(1);

        $container->setAlias(
            Argument::exact('swarrot.channel_factory.default'),
            Argument::exact('foo')
        )->shouldBeCalledTimes(1);

        $bag->remove(Argument::exact('swarrot.config'))
            ->shouldBeCalledTimes(1);

        $container->getParameterBag()
            ->willReturn($bag->reveal())
            ->shouldBeCalledTimes(1);

        $compiler = new ProviderPass;
        $compiler->process($container->reveal());
    }
}
