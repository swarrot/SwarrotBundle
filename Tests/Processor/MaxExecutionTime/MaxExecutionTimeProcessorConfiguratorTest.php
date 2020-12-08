<?php

namespace Swarrot\SwarrotBundle\Tests\Processor\MaxExecutionTime;

use Swarrot\SwarrotBundle\Processor\MaxExecutionTime\MaxExecutionTimeProcessorConfigurator;
use Swarrot\SwarrotBundle\Tests\Processor\ProcessorConfiguratorTestCase;

class MaxExecutionTimeProcessorConfiguratorTest extends ProcessorConfiguratorTestCase
{
    public function testItIsInitializable()
    {
        $configurator = new MaxExecutionTimeProcessorConfigurator(
            'Swarrot\Processor\MaxExecutionTime\MaxExecutionTimeProcessor',
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );
        $this->assertInstanceOf(
            'Swarrot\SwarrotBundle\Processor\MaxExecutionTime\MaxExecutionTimeProcessorConfigurator',
            $configurator
        );
    }

    public function testItUsedDefaultExtra()
    {
        $configurator = new MaxExecutionTimeProcessorConfigurator(
            'Swarrot\Processor\MaxExecutionTime\MaxExecutionTimeProcessor',
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );
        $configurator->setExtras(['max_execution_time' => 100]);
        $input = $this->getUserInput([], $configurator);

        $this->assertSame(['max_execution_time' => 100], $configurator->resolveOptions($input));
    }

    public function testItUsedUserInput()
    {
        $configurator = new MaxExecutionTimeProcessorConfigurator(
            'Swarrot\Processor\MaxExecutionTime\MaxExecutionTimeProcessor',
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );

        $configurator->setExtras([]);
        $input = $this->getUserInput(['--max-execution-time' => 200], $configurator);

        $this->assertSame(['max_execution_time' => 200], $configurator->resolveOptions($input));
    }

    public function testItUsedDefaultValue()
    {
        $configurator = new MaxExecutionTimeProcessorConfigurator(
            'Swarrot\Processor\MaxExecutionTime\MaxExecutionTimeProcessor',
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );

        $configurator->setExtras([]);
        $input = $this->getUserInput([], $configurator);

        $this->assertSame(['max_execution_time' => 300], $configurator->resolveOptions($input));
    }

    public function testItCanReturnsAValidProcessor()
    {
        $stubLogger = $this->prophesize('Psr\Log\LoggerInterface')->reveal();

        $configurator = new MaxExecutionTimeProcessorConfigurator(
            'Swarrot\Processor\MaxExecutionTime\MaxExecutionTimeProcessor',
            $stubLogger
        );

        $processor = $this->createProcessor($configurator);

        $this->assertInstanceOf('Swarrot\Processor\MaxExecutionTime\MaxExecutionTimeProcessor', $processor);
    }
}
