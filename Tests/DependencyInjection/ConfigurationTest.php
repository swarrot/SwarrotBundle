<?php

namespace Swarrot\SwarrotBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Swarrot\SwarrotBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Parser;

class ConfigurationTest extends TestCase
{
    public function test_with_default_configuration()
    {
        $parser = new Parser();
        $config = $parser->parse(file_get_contents(__DIR__.'/../fixtures/default_configuration.yml'));

        $configuration = new Configuration(true);
        $processor = new Processor();

        $processedConfiguration = $processor->processConfiguration($configuration, [$config['swarrot']]);
        $expectedDefaultConfiguration = require_once __DIR__.'/../fixtures/default_configuration.php';

        $this->assertSame($expectedDefaultConfiguration, $processedConfiguration);
    }
}
