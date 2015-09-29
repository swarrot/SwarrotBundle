<?php

namespace Swarrot\SwarrotBundle\Tests\DependencyInjection\Compiler;

use Swarrot\SwarrotBundle\DependencyInjection\Compiler\ProviderCompilerPass;

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
        $container = $this->getMock('Symfony\\Component\\DependencyInjection\\ContainerBuilder', [
            'has',
            'setAlias',
            'hasParameter',
            'getParameter',
            'getDefinition',
            'getParameterBag',
            'findTaggedServiceIds',
        ]);

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
        $container->expects($this->never())->method('getParameterBag');
        $container->expects($this->never())->method('findTaggedServiceIds');

        $compiler = new ProviderCompilerPass;
        $compiler->process($container);
    }

    public function test_should_not_run_if_not_configured()
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
        $container->expects($this->never())->method('getParameterBag');
        $container->expects($this->never())->method('findTaggedServiceIds');

        $compiler = new ProviderCompilerPass;
        $compiler->process($container);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Invalid provider "foo"
     */
    public function test_unexistant_provider()
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
                        'alias' => 'foo.bar'
                    ]
                ],
                'bar' => [
                    []
                ]
            ])
        ;
        $container
            ->expects($this->once())
            ->method('getParameter')
            ->with('swarrot.provider_config')
            ->willReturn([
                'foo',
                []
            ])
        ;

        $container->expects($this->never())->method('setAlias');
        $container->expects($this->never())->method('getDefinition');
        $container->expects($this->never())->method('getParameterBag');

        $compiler = new ProviderCompilerPass;
        $compiler->process($container);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage The provider "foo.bar" is not valid
     */
    public function test_invalid_provider()
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
        $definition = $this->getMock('Symfony\\Component\\DependencyInjection\\Definition');

        $definition->expects($this->once())
                   ->method('getClass')
                   ->with()
                   ->willReturn('stdClass')
        ;

        $definition->expects($this->never())->method('addMethodCall');

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
                        'alias' => 'foo.bar'
                    ]
                ],
                'bar' => [
                    []
                ]
            ])
        ;
        $container
            ->expects($this->once())
            ->method('getParameter')
            ->with('swarrot.provider_config')
            ->willReturn([
                'foo.bar',
                []
            ])
        ;
        $container
            ->expects($this->once())
            ->method('getDefinition')
            ->with('foo')
            ->willReturn($definition)
        ;

        $container->expects($this->never())->method('setAlias');
        $container->expects($this->never())->method('getParameterBag');

        $compiler = new ProviderCompilerPass;
        $compiler->process($container);
    }

    public function test_successful_provider()
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
        $definition = $this->getMock('Symfony\\Component\\DependencyInjection\\Definition');
        $parameterBag = $this->getMock('Symfony\\Component\\DependencyInjection\\ParameterBag\\ParameterBag');

        $definition->expects($this->once())
                   ->method('getClass')
                   ->with()
                   ->willReturn('Swarrot\\SwarrotBundle\\Broker\\FactoryInterface')
        ;
        $definition->expects($this->once())
                   ->method('addMethodCall')
                   ->with('addConnection', ['foo', []])
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
                        'alias' => 'foo.bar'
                    ]
                ],
                'bar' => [
                    []
                ]
            ])
        ;
        $container
            ->expects($this->once())
            ->method('getParameter')
            ->with('swarrot.provider_config')
            ->willReturn([
                'foo.bar',
                [
                    'foo' => []
                ]
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
            ->with('swarrot.factory.default', 'foo')
        ;
        $container
            ->expects($this->once())
            ->method('getParameterBag')
            ->willReturn($parameterBag)
        ;

        $parameterBag
            ->expects($this->once())
            ->method('remove')
            ->with('swarrot.provider_config')
        ;

        $compiler = new ProviderCompilerPass;
        $compiler->process($container);;
    }
}
