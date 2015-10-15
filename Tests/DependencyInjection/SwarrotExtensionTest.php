<?php

namespace Swarrot\SwarrotBundle\Tests\DependencyInjection;

use Swarrot\SwarrotBundle\DependencyInjection\SwarrotExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SwarrotExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_uses_the_default_connection_for_message_types()
    {
        $container = $this->createContainer();
        $config = array(
            'messages_types' => array(
                'test' => array('exchange' => 'test'),
            ),
        );

        $this->loadConfig($container, $config);

        $this->assertTrue($container->hasParameter('swarrot.messages_types'));

        $messagesTypes = $container->getParameter('swarrot.messages_types');

        $this->assertArrayHasKey('test', $messagesTypes);
        $expectedMessageType = array(
            'connection' => 'default',
            'exchange' => 'test',
            'routing_key' => null,
            'extras' => array(),
        );
        $this->assertEquals($expectedMessageType, $messagesTypes['test']);
    }

    public function test_it_registers_commands()
    {
        $container = $this->createContainer();
        $config = array(
            'consumers' => array(
                'testing' => array(
                    'processor' => 'app.swarrot_processor',
                ),
            ),
        );

        $this->loadConfig($container, $config);

        $this->assertHasService($container, 'swarrot.command.generated.testing');

        $this->assertTrue($container->hasParameter('swarrot.commands'));

        $commands = $container->getParameter('swarrot.commands');
        $this->assertArrayHasKey('testing', $commands);
        $this->assertSame('swarrot.command.generated.testing', $commands['testing']);
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

        $this->loadConfig($container, array('enable_collector' => false));

        $this->assertNotHasService($container, 'swarrot.data_collector');
    }

    public function test_it_registers_the_collector_when_explicitly_enabled()
    {
        $container = $this->createContainer(false);

        $this->loadConfig($container, array('enable_collector' => true));

        $this->assertHasService($container, 'swarrot.data_collector');
    }

    private function assertHasService(ContainerBuilder $container, $id)
    {
        $this->assertTrue($container->hasDefinition($id) || $container->hasAlias($id), sprintf('The service %s should be defined.', $id));
    }

    private function assertNotHasService(ContainerBuilder $container, $id)
    {
        $this->assertFalse($container->hasDefinition($id) || $container->hasAlias($id), sprintf('The service %s should not be defined.', $id));
    }

    private function loadConfig(ContainerBuilder $container, array $config = array())
    {
        // Minimal config required by the Configuration class
        $defaultConfig = array(
            'connections' => array('default' => null),
        );

        $extension = new SwarrotExtension();

        $extension->load(array($defaultConfig, $config), $container);
    }

    private function createContainer($debug = true)
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', $debug);

        return $container;
    }
}
