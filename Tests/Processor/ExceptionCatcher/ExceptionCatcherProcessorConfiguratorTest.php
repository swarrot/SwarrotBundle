<?php

namespace Swarrot\SwarrotBundle\Tests\Processor\ExceptionCatcher;

use Swarrot\SwarrotBundle\Processor\ExceptionCatcher\ExceptionCatcherProcessorConfigurator;
use Swarrot\SwarrotBundle\Tests\Processor\ProcessorConfiguratorTestCase;

class ExceptionCatcherProcessorConfiguratorTest extends ProcessorConfiguratorTestCase
{
    public function testItIsInitializable()
    {
        $configurator = new ExceptionCatcherProcessorConfigurator(
            'Swarrot\Processor\ExceptionCatcher\ExceptionCatcherProcessor',
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );
        $this->assertInstanceOf(
            'Swarrot\SwarrotBundle\Processor\ExceptionCatcher\ExceptionCatcherProcessorConfigurator',
            $configurator
        );
    }

    public function testItResolvesOptions()
    {
        $configurator = new ExceptionCatcherProcessorConfigurator(
            'Swarrot\Processor\ExceptionCatcher\ExceptionCatcherProcessor',
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );
        $configurator->setExtras([]);
        $input = $this->getUserInput([], $configurator);

        $this->assertSame([], $configurator->resolveOptions($input));
    }

    public function testItIsDisablable()
    {
        $configurator = new ExceptionCatcherProcessorConfigurator(
            'Swarrot\Processor\ExceptionCatcher\ExceptionCatcherProcessor',
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );
        $input = $this->getUserInput(['--no-catch' => true], $configurator);

        $configurator->resolveOptions($input);
        $this->assertFalse($configurator->isEnabled());
    }

    public function testItCanReturnsAValidProcessor()
    {
        $dummyConnection = $this->prophesize('Psr\Log\LoggerInterface')->reveal();

        $configurator = new ExceptionCatcherProcessorConfigurator(
            'Swarrot\Processor\ExceptionCatcher\ExceptionCatcherProcessor',
            $dummyConnection
        );

        $processor = $this->createProcessor($configurator, []);

        $this->assertInstanceOf('Swarrot\Processor\ExceptionCatcher\ExceptionCatcherProcessor', $processor);
    }
}
