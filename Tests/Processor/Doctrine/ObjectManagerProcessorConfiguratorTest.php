<?php

namespace Swarrot\SwarrotBundle\Tests\Processor\Doctrine;

use Swarrot\SwarrotBundle\Processor\Doctrine\ObjectManagerProcessorConfigurator;
use Swarrot\SwarrotBundle\Tests\Processor\ProcessorConfiguratorTestCase;

class ObjectManagerProcessorConfiguratorTest extends ProcessorConfiguratorTestCase
{
    public function test_it_is_initializable()
    {
        $configurator = new ObjectManagerProcessorConfigurator(
            'Swarrot\Processor\Doctrine\ObjectManagerProcessor',
            $this->prophesize('Doctrine\Persistence\ManagerRegistry')->reveal()
        );
        $this->assertInstanceOf(
            'Swarrot\SwarrotBundle\Processor\Doctrine\ObjectManagerProcessorConfigurator',
            $configurator
        );
    }

    public function test_it_resolves_options()
    {
        $configurator = new ObjectManagerProcessorConfigurator(
            'Swarrot\Processor\Doctrine\ObjectManagerProcessor',
            $this->prophesize('Doctrine\Persistence\ManagerRegistry')->reveal()
        );
        $configurator->setExtras([]);
        $input = $this->getUserInput([], $configurator);

        $this->assertSame([], $configurator->resolveOptions($input));
    }

    public function test_it_is_disablable()
    {
        $configurator = new ObjectManagerProcessorConfigurator(
            'Swarrot\Processor\Doctrine\ObjectManagerProcessor',
            $this->prophesize('Doctrine\Persistence\ManagerRegistry')->reveal()
        );
        $input = $this->getUserInput(['--no-reset' => true], $configurator);

        $configurator->resolveOptions($input);
        $this->assertFalse($configurator->isEnabled());
    }

    public function test_it_can_returns_a_valid_processor()
    {
        $dummyConnection = $this->prophesize('Doctrine\Persistence\ManagerRegistry')->reveal();

        $configurator = new ObjectManagerProcessorConfigurator(
            'Swarrot\Processor\Doctrine\ObjectManagerProcessor',
            $dummyConnection
        );

        $processor = $this->createProcessor($configurator, []);

        $this->assertInstanceOf('Swarrot\Processor\Doctrine\ObjectManagerProcessor', $processor);
    }
}
