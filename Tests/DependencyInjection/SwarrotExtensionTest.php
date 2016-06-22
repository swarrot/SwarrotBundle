<?php

namespace Swarrot\SwarrotBundle\Tests\DependencyInjection;

use Swarrot\SwarrotBundle\DependencyInjection\SwarrotExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SwarrotExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_uses_the_default_connection_for_message_types()
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

    public function test_it_registers_commands()
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

        $configurators = $container->getDefinition('swarrot.command.generated.testing')->getArgument(3);
        $this->assertInternalType('array', $configurators);
        $this->assertCount(1, $configurators);

        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $configurators[0]);
        $configuratorDefintion = $container->getDefinition((string) $configurators[0]);

        $this->assertCount(1, $configuratorDefintion->getMethodCalls());
        $method = $configuratorDefintion->getMethodCalls()[0];
        $this->assertEquals('setExtras', $method[0]);
        $this->assertEquals(['foo' => 'bar'], $method[1][0]);
    }

    /**
     * @group legacy
     */
    public function test_legacy_config_is_kept()
    {
        $container = $this->createContainer();
        $config = [
            'processors_stack' => [
                'ack' => 'AppBundle\\MyAckProcessor',
            ],
            'consumers' => [
                'testing' => [
                    'processor' => 'app.swarrot_processor',
                ],
            ],
        ];

        $this->loadConfig($container, $config);

        $this->assertHasService($container, 'swarrot.command.generated.testing');

        $configurators = $container->getDefinition('swarrot.command.generated.testing')->getArgument(3);
        $this->assertInternalType('array', $configurators);
        $this->assertCount(1, $configurators);

        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $configurators[0]);
        $configuratorDefintion = $container->getDefinition((string) $configurators[0]);
        $this->assertEquals('AppBundle\\MyAckProcessor', $configuratorDefintion->getArgument(0));
    }

    public function test_it_registers_the_collector_by_default_in_debug_mode()
    {
        $container = $this->createContainer();

        $this->loadConfig($container);

        $this->assertHasService($container, 'swarrot.data_collector');
    }

    public function test_it_does_not_register_the_collector_by_default_in_non_debug_mode()
    {
        $container = $this->createContainer(false);

        $this->loadConfig($container);

        $this->assertNotHasService($container, 'swarrot.data_collector');
    }

    public function test_it_does_not_register_the_collector_when_explicitly_disabled()
    {
        $container = $this->createContainer();

        $this->loadConfig($container, ['enable_collector' => false]);

        $this->assertNotHasService($container, 'swarrot.data_collector');
    }

    public function test_it_registers_the_collector_when_explicitly_enabled()
    {
        $container = $this->createContainer(false);

        $this->loadConfig($container, ['enable_collector' => true]);

        $this->assertHasService($container, 'swarrot.data_collector');
    }

    /**
     * @group legacy
     */
    public function test_it_use_the_asked_logger_with_deprecated_key()
    {
        $container = $this->createContainer(false);

        $this->loadConfig($container, ['publisher_logger' => 'my_awesome_logger']);

        $this->assertHasService($container, 'swarrot.logger');
        $alias = $container->getAlias('swarrot.logger');

        $this->assertEquals('my_awesome_logger', (string) $alias);
    }

    public function test_it_use_the_asked_logger_with_new_key()
    {
        $container = $this->createContainer(false);

        $this->loadConfig($container, ['logger' => 'my_awesome_logger']);

        $this->assertHasService($container, 'swarrot.logger');
        $alias = $container->getAlias('swarrot.logger');

        $this->assertEquals('my_awesome_logger', (string) $alias);
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
