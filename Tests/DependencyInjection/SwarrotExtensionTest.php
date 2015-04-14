<?php

namespace Swarrot\SwarrotBundle\Tests\DependencyInjection;

use Swarrot\SwarrotBundle\DependencyInjection\SwarrotExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SwarrotExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_is_initializable()
    {
        $this->assertInstanceOf(
            'Swarrot\SwarrotBundle\DependencyInjection\SwarrotExtension',
            new SwarrotExtension()
        );
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
            'provider' => 'pecl',
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
