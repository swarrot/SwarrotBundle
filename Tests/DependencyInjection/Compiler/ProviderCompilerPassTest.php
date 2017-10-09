<?php

namespace Swarrot\SwarrotBundle\Tests\DependencyInjection\Compiler;

use Swarrot\SwarrotBundle\DependencyInjection\Compiler\ProviderCompilerPass;
use Symfony\Component\DependencyInjection\Alias;

class ProviderCompilerPassTest extends \PHPUnit_Framework_TestCase
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
        $container = $this->getContainer();

        $container
            ->expects($this->once())
            ->method('has')
            ->with('swarrot.factory.default')
            ->willReturn(true)
        ;

        $container->expects($this->never())->method('setAlias');
        $container->expects($this->never())->method('hasParameter');
        $container->expects($this->never())->method('getParameter');
        $container->expects($this->never())->method('getDefinition');
        $container->expects($this->never())->method('findTaggedServiceIds');

        $compiler = new ProviderCompilerPass();
        $compiler->process($container);
    }

    public function test_should_not_run_if_not_configured()
    {
        $container = $this->getContainer();

        $container
            ->expects($this->once())
            ->method('has')
            ->with('swarrot.factory.default')
            ->willReturn(false)
        ;
        $container
            ->expects($this->once())
            ->method('hasParameter')
            ->with('swarrot.provider_config')
            ->willReturn(false)
        ;

        $container->expects($this->never())->method('setAlias');
        $container->expects($this->never())->method('getParameter');
        $container->expects($this->never())->method('getDefinition');
        $container->expects($this->never())->method('findTaggedServiceIds');

        $compiler = new ProviderCompilerPass();
        $compiler->process($container);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage The provider's alias is no defined for the service "foo"
     */
    public function test_missing_alias()
    {
        $container = $this->getContainer();

        $container
            ->expects($this->once())
            ->method('has')
            ->with('swarrot.factory.default')
            ->willReturn(false)
        ;
        $container
            ->expects($this->once())
            ->method('hasParameter')
            ->with('swarrot.provider_config')
            ->willReturn(true)
        ;
        $container
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with('swarrot.provider_factory')
            ->willReturn([
                'foo' => [
                    [],
                ],
            ])
        ;

        $container->expects($this->never())->method('getParameter');
        $container->expects($this->never())->method('setAlias');
        $container->expects($this->never())->method('getDefinition');

        $compiler = new ProviderCompilerPass();
        $compiler->process($container);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Invalid provider "foo"
     */
    public function test_unexistant_provider()
    {
        $container = $this->getContainer();

        $container
            ->expects($this->once())
            ->method('has')
            ->with('swarrot.factory.default')
            ->willReturn(false)
        ;
        $container
            ->expects($this->once())
            ->method('hasParameter')
            ->with('swarrot.provider_config')
            ->willReturn(true)
        ;
        $container
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with('swarrot.provider_factory')
            ->willReturn([
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
            ])
        ;
        $container
            ->expects($this->once())
            ->method('getParameter')
            ->with('swarrot.provider_config')
            ->willReturn([
                'foo',
                [],
            ])
        ;

        $container->expects($this->never())->method('setAlias');
        $container->expects($this->never())->method('getDefinition');

        $compiler = new ProviderCompilerPass();
        $compiler->process($container);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage The provider "foo.bar" is not valid
     */
    public function test_invalid_provider()
    {
        $container = $this->getContainer();
        $definition = $this->getMock('Symfony\\Component\\DependencyInjection\\Definition');

        $definition->expects($this->once())
                   ->method('getClass')
                   ->with()
                   ->willReturn('stdClass')
        ;
        $definition->expects($this->never())->method('addMethodCall');

        $container->getParameterBag()
                  ->expects($this->once())
                  ->method('resolveValue')
                  ->with('stdClass')
                  ->willReturn('stdClass')
        ;

        $container
            ->expects($this->once())
            ->method('has')
            ->with('swarrot.factory.default')
            ->willReturn(false)
        ;
        $container
            ->expects($this->once())
            ->method('hasParameter')
            ->with('swarrot.provider_config')
            ->willReturn(true)
        ;
        $container
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with('swarrot.provider_factory')
            ->willReturn([
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
            ])
        ;
        $container
            ->expects($this->once())
            ->method('getParameter')
            ->with('swarrot.provider_config')
            ->willReturn([
                'foo.bar',
                [],
            ])
        ;
        $container
            ->expects($this->once())
            ->method('getDefinition')
            ->with('foo')
            ->willReturn($definition)
        ;

        $container->expects($this->never())->method('setAlias');

        $compiler = new ProviderCompilerPass();
        $compiler->process($container);
    }

    public function test_successful_provider()
    {
        $container = $this->getContainer();
        $definition = $this->getMock('Symfony\\Component\\DependencyInjection\\Definition');

        $definition->expects($this->once())
                   ->method('getClass')
                   ->with()
                   ->willReturn('Swarrot\\SwarrotBundle\\Broker\\FactoryInterface')
        ;
        $definition->expects($this->once())
                   ->method('addMethodCall')
                   ->with('addConnection', ['foo', []])
        ;

        $container->getParameterBag()
                  ->expects($this->once())
                  ->method('resolveValue')
                  ->with('Swarrot\\SwarrotBundle\\Broker\\FactoryInterface')
                  ->willReturn('Swarrot\\SwarrotBundle\\Broker\\FactoryInterface')
        ;

        $container
            ->expects($this->once())
            ->method('has')
            ->with('swarrot.factory.default')
            ->willReturn(false)
        ;
        $container
            ->expects($this->once())
            ->method('hasParameter')
            ->with('swarrot.provider_config')
            ->willReturn(true)
        ;
        $container
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with('swarrot.provider_factory')
            ->willReturn([
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
            ])
        ;
        $container
            ->expects($this->once())
            ->method('getParameter')
            ->with('swarrot.provider_config')
            ->willReturn([
                'foo.bar',
                [
                    'foo' => [],
                ],
            ])
        ;
        $container
            ->expects($this->once())
            ->method('getDefinition')
            ->with('foo')
            ->willReturn($definition)
        ;
        $container
            ->expects($this->once())
            ->method('setAlias')
            ->with('swarrot.factory.default', new Alias('foo', true))
        ;

        $compiler = new ProviderCompilerPass();
        $compiler->process($container);
    }

    private function getContainer()
    {
        $container = $this->getMock('Symfony\\Component\\DependencyInjection\\ContainerBuilder', [
            'has',
            'setAlias',
            'hasParameter',
            'getParameter',
            'getDefinition',
            'getParameterBag',
            'findTaggedServiceIds',
        ]);
        $parameterBag = $this->getMock('Symfony\\Component\\DependencyInjection\\ParameterBag\\ParameterBag');

        $container
            ->expects($this->any())
            ->method('getParameterBag')
            ->willReturn($parameterBag)
        ;

        return $container;
    }
}
