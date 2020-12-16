<?php

namespace Swarrot\SwarrotBundle\Tests\Processor\InstantRetry;

use Swarrot\SwarrotBundle\Processor\InstantRetry\InstantRetryProcessorConfigurator;
use Swarrot\SwarrotBundle\Tests\Processor\ProcessorConfiguratorTestCase;

class InstantRetryProcessorConfiguratorTest extends ProcessorConfiguratorTestCase
{
    public function testItIsInitializable()
    {
        $configurator = new InstantRetryProcessorConfigurator(
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );
        $this->assertInstanceOf(
            'Swarrot\SwarrotBundle\Processor\InstantRetry\InstantRetryProcessorConfigurator',
            $configurator
        );
    }

    public function testItResolvesOptions()
    {
        $configurator = new InstantRetryProcessorConfigurator(
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );
        $configurator->setExtras([]);
        $input = $this->getUserInput([], $configurator);

        $this->assertSame([], $configurator->resolveOptions($input));
    }

    public function testItCanReturnsAValidProcessor()
    {
        $dummyConnection = $this->prophesize('Psr\Log\LoggerInterface')->reveal();

        $configurator = new InstantRetryProcessorConfigurator(
            $dummyConnection
        );

        $processor = $this->createProcessor($configurator, []);

        $this->assertInstanceOf('Swarrot\Processor\InstantRetry\InstantRetryProcessor', $processor);
    }
}
