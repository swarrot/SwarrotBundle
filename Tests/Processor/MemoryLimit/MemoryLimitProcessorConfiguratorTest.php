<?php

namespace Swarrot\SwarrotBundle\Tests\Processor\MemoryLimit;

use Swarrot\SwarrotBundle\Processor\MemoryLimit\MemoryLimitProcessorConfigurator;
use Swarrot\SwarrotBundle\Tests\Processor\ProcessorConfiguratorTestCase;

class MemoryLimitProcessorConfiguratorTest extends ProcessorConfiguratorTestCase
{
    public function testItIsInitializable()
    {
        $configurator = new MemoryLimitProcessorConfigurator(
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );
        $this->assertInstanceOf(
            'Swarrot\SwarrotBundle\Processor\MemoryLimit\MemoryLimitProcessorConfigurator',
            $configurator
        );
    }

    public function testItResolvesOptions()
    {
        $configurator = new MemoryLimitProcessorConfigurator(
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );
        $configurator->setExtras([]);
        $input = $this->getUserInput([], $configurator);

        $this->assertSame([], $configurator->resolveOptions($input));
    }

    public function testItCanReturnsAValidProcessor()
    {
        $dummyConnection = $this->prophesize('Psr\Log\LoggerInterface')->reveal();

        $configurator = new MemoryLimitProcessorConfigurator(
            $dummyConnection
        );

        $processor = $this->createProcessor($configurator, []);

        $this->assertInstanceOf('Swarrot\Processor\MemoryLimit\MemoryLimitProcessor', $processor);
    }
}
