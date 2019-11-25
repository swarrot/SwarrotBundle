<?php

namespace Swarrot\SwarrotBundle\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Swarrot\SwarrotBundle\Broker\FactoryInterface;
use Swarrot\SwarrotBundle\DependencyInjection\Compiler\ProviderCompilerPass;
use Symfony\Component\DependencyInjection\Alias;

class ProviderCompilerPassTest extends TestCase
{
    public function test_it_is_initializable()
    {
        $this->assertInstanceOf(
            'Swarrot\\SwarrotBundle\\DependencyInjection\\Compiler\\ProviderCompilerPass',
            new ProviderCompilerPass()
        );
    }

    public function test_should_not_run_if_already_declared()
    {
        $container = $this->prophesize('Symfony\\Component\\DependencyInjection\\ContainerBuilder');

        $container->has('swarrot.factory.default')->willReturn(true);
        $container->setAlias()->shouldNotBeCalled();
        $container->hasParameter()->shouldNotBeCalled();
        $container->getParameter()->shouldNotBeCalled();
        $container->getDefinition()->shouldNotBeCalled();
        $container->findTaggedServiceIds()->shouldNotBeCalled();

        $compiler = new ProviderCompilerPass();
        $compiler->process($container->reveal());
    }

    public function test_should_not_run_if_not_configured()
    {
        $container = $this->prophesize('Symfony\\Component\\DependencyInjection\\ContainerBuilder');

        $container->has('swarrot.factory.default')->willReturn(false);
        $container->hasParameter('swarrot.provider_config')->shouldBeCalledTimes(1)->willReturn(false);

        $container->setAlias()->shouldNotBeCalled();
        $container->getParameter()->shouldNotBeCalled();
        $container->getDefinition()->shouldNotBeCalled();
        $container->findTaggedServiceIds()->shouldNotBeCalled();

        $compiler = new ProviderCompilerPass();
        $compiler->process($container->reveal());
    }

    public function test_missing_alias()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The provider\'s alias is no defined for the service "foo"');

        $container = $this->prophesize('Symfony\\Component\\DependencyInjection\\ContainerBuilder');

        $container->has('swarrot.factory.default')->willReturn(false);
        $container->hasParameter('swarrot.provider_config')->shouldBeCalledTimes(1)->willReturn(true);
        $container->findTaggedServiceIds('swarrot.provider_factory')->shouldBeCalledTimes(1)->willReturn([
            'foo' => [
                [],
            ],
        ]);

        $container->setAlias()->shouldNotBeCalled();
        $container->getParameter()->shouldNotBeCalled();
        $container->getDefinition()->shouldNotBeCalled();

        $compiler = new ProviderCompilerPass();
        $compiler->process($container->reveal());
    }

    public function test_unexistant_provider()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid provider "foo"');

        $container = $this->prophesize('Symfony\\Component\\DependencyInjection\\ContainerBuilder');

        $container->has('swarrot.factory.default')->willReturn(false);
        $container->hasParameter('swarrot.provider_config')->shouldBeCalledTimes(1)->willReturn(true);
        $container->findTaggedServiceIds('swarrot.provider_factory')->shouldBeCalledTimes(1)->willReturn([
            'foo' => [
                [
                    'alias' => 'foo.bar',
                ],
            ],
            'bar' => [
                [
                    'alias' => 'bar',
                ],
            ],
        ]);
        $container->getParameter('swarrot.provider_config')->shouldBeCalledTimes(1)->willReturn([
            'foo',
            [],
        ]);

        $container->setAlias()->shouldNotBeCalled();
        $container->getDefinition()->shouldNotBeCalled();

        $compiler = new ProviderCompilerPass();
        $compiler->process($container->reveal());
    }

    public function test_invalid_provider()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The provider "foo.bar" is not valid');

        $container = $this->prophesize('Symfony\\Component\\DependencyInjection\\ContainerBuilder');
        $definition = $this->prophesize('Symfony\\Component\\DependencyInjection\\Definition');

        $stdClass = new \stdClass();

        $definition->getClass()->willReturn($stdClass);
        $definition->addMethodCall()->shouldNotBeCalled();

        $parameterBag = $this->prophesize('Symfony\\Component\\DependencyInjection\\ParameterBag\\ParameterBag');
        $parameterBag->resolveValue($stdClass)->willReturn(new \stdClass())->shouldBeCalledTimes(1);
        $container->getParameterBag()->willReturn($parameterBag->reveal());

        $container->has('swarrot.factory.default')->willReturn(false);
        $container->hasParameter('swarrot.provider_config')->shouldBeCalledTimes(1)->willReturn(true);
        $container->findTaggedServiceIds('swarrot.provider_factory')->shouldBeCalledTimes(1)->willReturn([
            'foo' => [
                [
                    'alias' => 'foo.bar',
                ],
            ],
            'bar' => [
                [
                    'alias' => 'bar',
                ],
            ],
        ]);
        $container->getParameter('swarrot.provider_config')->shouldBeCalledTimes(1)->willReturn([
            'foo.bar',
            [],
        ]);

        $container->getDefinition('foo')->willReturn($definition)->shouldBeCalledTimes(1);
        $container->setAlias()->shouldNotBeCalled();

        $compiler = new ProviderCompilerPass();
        $compiler->process($container->reveal());
    }

    public function test_successful_provider()
    {
        $container = $this->prophesize('Symfony\\Component\\DependencyInjection\\ContainerBuilder');
        $definition = $this->prophesize('Symfony\\Component\\DependencyInjection\\Definition');

        $definition->getClass()->willReturn(FactoryInterface::class)->shouldBeCalledTimes(1);
        $definition->addMethodCall('addConnection', ['foo', []])->shouldBeCalledTimes(1);

        $parameterBag = $this->prophesize('Symfony\\Component\\DependencyInjection\\ParameterBag\\ParameterBag');
        $parameterBag->remove('swarrot.provider_config')->shouldBeCalledTimes(1);
        $parameterBag->resolveValue(FactoryInterface::class)->willReturnArgument(0)->shouldBeCalledTimes(1);
        $container->getParameterBag()->willReturn($parameterBag->reveal());

        $container->has('swarrot.factory.default')->willReturn(false);
        $container->hasParameter('swarrot.provider_config')->shouldBeCalledTimes(1)->willReturn(true);
        $container->findTaggedServiceIds('swarrot.provider_factory')->shouldBeCalledTimes(1)->willReturn([
            'foo' => [
                [
                    'alias' => 'foo.bar',
                ],
            ],
            'bar' => [
                [
                    'alias' => 'bar',
                ],
            ],
        ]);
        $container->getParameter('swarrot.provider_config')->shouldBeCalledTimes(1)->willReturn([
            'foo.bar',
            [
                'foo' => [],
            ],
        ]);

        $container->getDefinition('foo')->willReturn($definition)->shouldBeCalledTimes(1);
        $container->setAlias('swarrot.factory.default', new Alias('foo', true))->shouldBeCalledTimes(1);

        $compiler = new ProviderCompilerPass();
        $compiler->process($container->reveal());
    }
}
