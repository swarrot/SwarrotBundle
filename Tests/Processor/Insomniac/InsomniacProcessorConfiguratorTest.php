<?php

namespace Swarrot\SwarrotBundle\Tests\Processor\Insomniac;

use Swarrot\SwarrotBundle\Processor\Insomniac\InsomniacProcessorConfigurator;
use Swarrot\SwarrotBundle\Tests\Processor\ProcessorConfiguratorTestCase;

class InsomniacProcessorConfiguratorTest extends ProcessorConfiguratorTestCase
{
    public function testItIsInitializable()
    {
        $configurator = new InsomniacProcessorConfigurator(
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );
        $this->assertInstanceOf(
            'Swarrot\SwarrotBundle\Processor\Insomniac\InsomniacProcessorConfigurator',
            $configurator
        );
    }

    public function testItResolvesOptions()
    {
        $configurator = new InsomniacProcessorConfigurator(
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );
        $configurator->setExtras([]);
        $input = $this->getUserInput([], $configurator);

        $this->assertSame([], $configurator->resolveOptions($input));
    }

    public function testItCanReturnsAValidProcessor()
    {
        $dummyConnection = $this->prophesize('Psr\Log\LoggerInterface')->reveal();

        $configurator = new InsomniacProcessorConfigurator(
            $dummyConnection
        );

        $processor = $this->createProcessor($configurator, []);

        $this->assertInstanceOf('Swarrot\Processor\Insomniac\InsomniacProcessor', $processor);
    }
}
