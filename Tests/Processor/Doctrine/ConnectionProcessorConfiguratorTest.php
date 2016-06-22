<?php

namespace Swarrot\SwarrotBundle\Tests\Processor\Doctrine;

use Swarrot\SwarrotBundle\Processor\Doctrine\ConnectionProcessorConfigurator;
use Swarrot\SwarrotBundle\Tests\Processor\ProcessorConfiguratorTestCase;

class ConnectionProcessorConfiguratorTest extends ProcessorConfiguratorTestCase
{
    public function test_it_is_initializable()
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

    public function test_it_resolves_options()
    {
        $configurator = new ConnectionProcessorConfigurator(
            'Swarrot\Processor\Doctrine\ConnectionProcessor',
            [$this->prophesize('Doctrine\DBAL\Connection')->reveal()]
        );
        $configurator->setExtras([]);
        $input = $this->getUserInput([], $configurator);

        $this->assertSame([], $configurator->resolveOptions($input));
    }

    public function test_it_can_returns_a_valid_processor()
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
