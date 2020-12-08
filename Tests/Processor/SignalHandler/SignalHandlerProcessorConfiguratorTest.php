<?php

namespace Swarrot\SwarrotBundle\Tests\Processor\SignalHandler;

use Swarrot\SwarrotBundle\Processor\SignalHandler\SignalHandlerProcessorConfigurator;
use Swarrot\SwarrotBundle\Tests\Processor\ProcessorConfiguratorTestCase;

class SignalHandlerProcessorConfiguratorTest extends ProcessorConfiguratorTestCase
{
    public function testItIsInitializable()
    {
        $configurator = new SignalHandlerProcessorConfigurator(
            'Swarrot\Processor\SignalHandler\SignalHandlerProcessor',
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );
        $this->assertInstanceOf(
            'Swarrot\SwarrotBundle\Processor\SignalHandler\SignalHandlerProcessorConfigurator',
            $configurator
        );
    }

    public function testItResolvesOptions()
    {
        $configurator = new SignalHandlerProcessorConfigurator(
            'Swarrot\Processor\SignalHandler\SignalHandlerProcessor',
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );
        $configurator->setExtras([]);
        $input = $this->getUserInput([], $configurator);

        $this->assertSame([], $configurator->resolveOptions($input));
    }

    public function testItCanReturnsAValidProcessor()
    {
        $dummyConnection = $this->prophesize('Psr\Log\LoggerInterface')->reveal();

        $configurator = new SignalHandlerProcessorConfigurator(
            'Swarrot\Processor\SignalHandler\SignalHandlerProcessor',
            $dummyConnection
        );

        $processor = $this->createProcessor($configurator, []);

        $this->assertInstanceOf('Swarrot\Processor\SignalHandler\SignalHandlerProcessor', $processor);
    }
}
