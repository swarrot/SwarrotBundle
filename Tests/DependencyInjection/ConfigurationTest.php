<?php

namespace SwarrotSwarrotBundle\Tests\DependencyInjection;

use SwarrotSwarrotBundle\Tests\TestCase;
use Symfony\Component\Config\Definition\Processor;
use SwarrotSwarrotBundle\DependencyInjection\Configuration;

class ConfigurationTest extends \PHPUnitFrameworkTestCase
{
    public function test_it_is_initilizable()
    {
        $this->assertInstanceOf(
            'SwarrotSwarrotBundle\DependencyInjection\Configuration',
            new Configuration()
        );
    }

    public function test_default_config()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), array());

        $this->assertEquals(
            self::getBundleDefaultConfig(),
            $config
        );
    }

    protected static function getBundleDefaultConfig()
    {
        return array(
            'provider'           => 'pecl',
            'default_connection' => null,
            'default_command'    => 'swarrot.command.base',
        );
    }
}
