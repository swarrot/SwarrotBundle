<?php

namespace Swarrot\SwarrotBundle\Tests\Processor\Doctrine;

use Swarrot\SwarrotBundle\Processor\Doctrine\ObjectManagerProcessorConfigurator;
use Swarrot\SwarrotBundle\Tests\Processor\ProcessorConfiguratorTestCase;

class ObjectManagerProcessorConfiguratorTest extends ProcessorConfiguratorTestCase
{
    public function testItIsInitializable()
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

    public function testItResolvesOptions()
    {
        $configurator = new ObjectManagerProcessorConfigurator(
            'Swarrot\Processor\Doctrine\ObjectManagerProcessor',
            $this->prophesize('Doctrine\Persistence\ManagerRegistry')->reveal()
        );
        $configurator->setExtras([]);
        $input = $this->getUserInput([], $configurator);

        $this->assertSame([], $configurator->resolveOptions($input));
    }

    public function testItIsDisablable()
    {
        $configurator = new ObjectManagerProcessorConfigurator(
            'Swarrot\Processor\Doctrine\ObjectManagerProcessor',
            $this->prophesize('Doctrine\Persistence\ManagerRegistry')->reveal()
        );
        $input = $this->getUserInput(['--no-reset' => true], $configurator);

        $configurator->resolveOptions($input);
        $this->assertFalse($configurator->isEnabled());
    }

    public function testItCanReturnsAValidProcessor()
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
