<?php

namespace Swarrot\SwarrotBundle\Tests\Processor\MemoryLimit;

use Swarrot\SwarrotBundle\Processor\MemoryLimit\MemoryLimitProcessorConfigurator;
use Swarrot\SwarrotBundle\Tests\Processor\ProcessorConfiguratorTestCase;

class MemoryLimitProcessorConfiguratorTest extends ProcessorConfiguratorTestCase
{
    public function test_it_is_initializable()
    {
        $configurator = new MemoryLimitProcessorConfigurator(
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );
        $this->assertInstanceOf(
            'Swarrot\SwarrotBundle\Processor\MemoryLimit\MemoryLimitProcessorConfigurator',
            $configurator
        );
    }

    public function test_it_resolves_options()
    {
        $configurator = new MemoryLimitProcessorConfigurator(
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );
        $configurator->setExtras([]);
        $input = $this->getUserInput([], $configurator);

        $this->assertSame([], $configurator->resolveOptions($input));
    }

    public function test_it_can_returns_a_valid_processor()
    {
        $dummyConnection = $this->prophesize('Psr\Log\LoggerInterface')->reveal();

        $configurator = new MemoryLimitProcessorConfigurator(
            $dummyConnection
        );

        $processor = $this->createProcessor($configurator, []);

        $this->assertInstanceOf('Swarrot\Processor\MemoryLimit\MemoryLimitProcessor', $processor);
    }
}
