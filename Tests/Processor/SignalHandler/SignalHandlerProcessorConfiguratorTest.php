<?php

namespace Swarrot\SwarrotBundle\Tests\Processor\SignalHandler;

use Swarrot\SwarrotBundle\Processor\SignalHandler\SignalHandlerProcessorConfigurator;
use Swarrot\SwarrotBundle\Tests\Processor\ProcessorConfiguratorTestCase;

class SignalHandlerProcessorConfiguratorTest extends ProcessorConfiguratorTestCase
{
    public function test_it_is_initializable()
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

    public function test_it_resolves_options()
    {
        $configurator = new SignalHandlerProcessorConfigurator(
            'Swarrot\Processor\SignalHandler\SignalHandlerProcessor',
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );
        $configurator->setExtras([]);
        $input = $this->getUserInput([], $configurator);

        $this->assertSame([], $configurator->resolveOptions($input));
    }

    public function test_it_can_returns_a_valid_processor()
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
