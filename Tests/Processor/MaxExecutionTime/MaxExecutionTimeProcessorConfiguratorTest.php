<?php

namespace Swarrot\SwarrotBundle\Tests\Processor\MaxExecutionTime;

use Swarrot\SwarrotBundle\Processor\MaxExecutionTime\MaxExecutionTimeProcessorConfigurator;
use Swarrot\SwarrotBundle\Tests\Processor\ProcessorConfiguratorTestCase;

class MaxExecutionTimeProcessorConfiguratorTest extends ProcessorConfiguratorTestCase
{
    public function test_it_is_initializable()
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

    public function test_it_used_default_extra()
    {
        $configurator = new MaxExecutionTimeProcessorConfigurator(
            'Swarrot\Processor\MaxExecutionTime\MaxExecutionTimeProcessor',
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );
        $configurator->setExtras(['max_execution_time' => 100]);
        $input = $this->getUserInput([], $configurator);

        $this->assertSame(['max_execution_time' => 100], $configurator->resolveOptions($input));
    }

    public function test_it_used_user_input()
    {
        $configurator = new MaxExecutionTimeProcessorConfigurator(
            'Swarrot\Processor\MaxExecutionTime\MaxExecutionTimeProcessor',
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );

        $configurator->setExtras([]);
        $input = $this->getUserInput(['--max-execution-time' => 200], $configurator);

        $this->assertSame(['max_execution_time' => 200], $configurator->resolveOptions($input));
    }

    public function test_it_used_default_value()
    {
        $configurator = new MaxExecutionTimeProcessorConfigurator(
            'Swarrot\Processor\MaxExecutionTime\MaxExecutionTimeProcessor',
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );

        $configurator->setExtras([]);
        $input = $this->getUserInput([], $configurator);

        $this->assertSame(['max_execution_time' => 300], $configurator->resolveOptions($input));
    }

    public function test_it_can_returns_a_valid_processor()
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
