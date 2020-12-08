<?php

namespace Swarrot\SwarrotBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Swarrot\SwarrotBundle\DependencyInjection\SwarrotExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SwarrotExtensionTest extends TestCase
{
    public function testItUsesTheDefaultConnectionForMessageTypes()
    {
        $container = $this->createContainer();
        $config = [
            'messages_types' => [
                'test' => ['exchange' => 'test'],
            ],
        ];

        $this->loadConfig($container, $config);

        $this->assertTrue($container->hasParameter('swarrot.messages_types'));

        $messagesTypes = $container->getParameter('swarrot.messages_types');

        $this->assertArrayHasKey('test', $messagesTypes);
        $expectedMessageType = [
            'connection' => 'default',
            'exchange' => 'test',
            'routing_key' => null,
            'extras' => [],
        ];
        $this->assertEquals($expectedMessageType, $messagesTypes['test']);
    }

    public function testItRegistersCommands()
    {
        $container = $this->createContainer();
        $config = [
            'consumers' => [
                'testing' => [
                    'processor' => 'app.swarrot_processor',
                    'middleware_stack' => [
                        [
                            'configurator' => 'swarrot.processor.ack',
                            'extras' => [
                                'foo' => 'bar',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->loadConfig($container, $config);

        $this->assertHasService($container, 'swarrot.command.generated.testing');

        $this->assertTrue($container->hasParameter('swarrot.commands'));

        $commands = $container->getParameter('swarrot.commands');
        $this->assertArrayHasKey('testing', $commands);
        $this->assertSame('swarrot.command.generated.testing', $commands['testing']);

        $testingCommandDefinition = $container->getDefinition('swarrot.command.generated.testing');
        $configurators = $testingCommandDefinition->getArgument(4);
        $this->assertIsArray($configurators);
        $this->assertCount(1, $configurators);
        $this->assertTrue($testingCommandDefinition->isPublic());

        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', array_values($configurators)[0]);
        $configuratorDefinition = $container->getDefinition((string) array_values($configurators)[0]);

        $this->assertCount(1, $configuratorDefinition->getMethodCalls());
        $method = $configuratorDefinition->getMethodCalls()[0];
        $this->assertEquals('setExtras', $method[0]);
        $this->assertEquals(['foo' => 'bar'], $method[1][0]);
    }

    public function testItRegistersTheCollectorByDefaultInDebugMode()
    {
        $container = $this->createContainer();

        $this->loadConfig($container);

        $this->assertHasService($container, 'swarrot.data_collector');
    }

    public function testItDoesNotRegisterTheCollectorByDefaultInNonDebugMode()
    {
        $container = $this->createContainer(false);

        $this->loadConfig($container);

        $this->assertNotHasService($container, 'swarrot.data_collector');
    }

    public function testItDoesNotRegisterTheCollectorWhenExplicitlyDisabled()
    {
        $container = $this->createContainer();

        $this->loadConfig($container, ['enable_collector' => false]);

        $this->assertNotHasService($container, 'swarrot.data_collector');
    }

    public function testItRegistersTheCollectorWhenExplicitlyEnabled()
    {
        $container = $this->createContainer(false);

        $this->loadConfig($container, ['enable_collector' => true]);

        $this->assertHasService($container, 'swarrot.data_collector');
    }

    public function testItUseTheAskedLoggerWithNewKey()
    {
        $container = $this->createContainer(false);

        $this->loadConfig($container, ['logger' => 'my_awesome_logger']);

        $this->assertHasService($container, 'swarrot.logger');
        $alias = $container->getAlias('swarrot.logger');

        $this->assertEquals('my_awesome_logger', (string) $alias);
    }

    public function testItExposesThePublisherService()
    {
        $container = $this->createContainer(false);
        $this->loadConfig($container);

        $this->assertTrue($container->getDefinition('swarrot.publisher')->isPublic());
    }

    private function assertHasService(ContainerBuilder $container, $id)
    {
        $this->assertTrue(
            $container->hasDefinition($id) || $container->hasAlias($id),
            sprintf('The service %s should be defined.', $id)
        );
    }

    private function assertNotHasService(ContainerBuilder $container, $id)
    {
        $this->assertFalse(
            $container->hasDefinition($id) || $container->hasAlias($id),
            sprintf('The service %s should not be defined.', $id)
        );
    }

    private function loadConfig(ContainerBuilder $container, array $config = [])
    {
        // Minimal config required by the Configuration class
        $defaultConfig = [
            'connections' => ['default' => null],
        ];

        $extension = new SwarrotExtension();

        $extension->load([$defaultConfig, $config], $container);
    }

    private function createContainer($debug = true)
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', $debug);

        return $container;
    }
}
