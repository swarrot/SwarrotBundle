<?php

namespace Swarrot\SwarrotBundle\Tests\Processor\Doctrine;

use Swarrot\SwarrotBundle\Processor\Doctrine\ConnectionProcessorConfigurator;
use Swarrot\SwarrotBundle\Tests\Processor\ProcessorConfiguratorTestCase;

class ConnectionProcessorConfiguratorTest extends ProcessorConfiguratorTestCase
{
    public function testItIsInitializable()
    {
        $configurator = new ConnectionProcessorConfigurator(
            'Swarrot\Processor\Doctrine\ConnectionProcessor',
            [$this->prophesize('Doctrine\DBAL\Connection')->reveal()]
        );
        $this->assertInstanceOf(
            'Swarrot\SwarrotBundle\Processor\Doctrine\ConnectionProcessorConfigurator',
            $configurator
        );
    }

    public function testItResolvesOptions()
    {
        $configurator = new ConnectionProcessorConfigurator(
            'Swarrot\Processor\Doctrine\ConnectionProcessor',
            [$this->prophesize('Doctrine\DBAL\Connection')->reveal()]
        );
        $configurator->setExtras([]);
        $input = $this->getUserInput([], $configurator);

        $this->assertSame([], $configurator->resolveOptions($input));
    }

    public function testItCanReturnsAValidProcessor()
    {
        $dummyConnection = [$this->prophesize('Doctrine\DBAL\Connection')->reveal()];

        $configurator = new ConnectionProcessorConfigurator(
            'Swarrot\Processor\Doctrine\ConnectionProcessor',
            $dummyConnection
        );

        $processor = $this->createProcessor($configurator, []);

        $this->assertInstanceOf('Swarrot\Processor\Doctrine\ConnectionProcessor', $processor);
    }
}
