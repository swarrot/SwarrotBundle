<?php

namespace Swarrot\SwarrotBundle\Tests\Processor\Retry;

use Swarrot\SwarrotBundle\Processor\Retry\RetryProcessorConfigurator;
use Swarrot\SwarrotBundle\Tests\Processor\ProcessorConfiguratorTestCase;

class RetryProcessorConfiguratorTest extends ProcessorConfiguratorTestCase
{
    public function testItIsInitializable()
    {
        $configurator = new RetryProcessorConfigurator(
            'Swarrot\Processor\Retry\RetryProcessor',
            $this->prophesize('Swarrot\SwarrotBundle\Broker\FactoryInterface')->reveal(),
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );
        $this->assertInstanceOf('Swarrot\SwarrotBundle\Processor\Retry\RetryProcessorConfigurator', $configurator);
    }

    public function testItUsedDefaultExtra()
    {
        $configurator = new RetryProcessorConfigurator(
            'Swarrot\Processor\Retry\RetryProcessor',
            $this->prophesize('Swarrot\SwarrotBundle\Broker\FactoryInterface')->reveal(),
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );
        $configurator->setExtras(
            [
                'retry_routing_key_pattern' => 'my_queue',
                'retry_attempts' => 4,
                'retry_log_levels_map' => ['\Exception' => 'error'],
                'retry_fail_log_levels_map' => ['\Exception' => 'error'],
            ]
        );
        $input = $this->getUserInput([], $configurator);

        $this->assertSame(
            [
                'retry_key_pattern' => 'my_queue',
                'retry_attempts' => 4,
                'retry_log_levels_map' => ['\Exception' => 'error'],
                'retry_fail_log_levels_map' => ['\Exception' => 'error'],
            ],
            $configurator->resolveOptions($input)
        );
    }

    public function testItUsedUserInput()
    {
        $configurator = new RetryProcessorConfigurator(
            'Swarrot\Processor\Retry\RetryProcessor',
            $this->prophesize('Swarrot\SwarrotBundle\Broker\FactoryInterface')->reveal(),
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );

        $configurator->setExtras([]);
        $input = $this->getUserInput(['--retry-attempts' => 5], $configurator);

        $this->assertSame(
            [
                'retry_key_pattern' => 'retry_%attempt%s',
                'retry_attempts' => 5,
                'retry_log_levels_map' => [],
                'retry_fail_log_levels_map' => [],
            ],
            $configurator->resolveOptions($input)
        );
    }

    public function testItUsedDefaultValue()
    {
        $configurator = new RetryProcessorConfigurator(
            'Swarrot\Processor\Retry\RetryProcessor',
            $this->prophesize('Swarrot\SwarrotBundle\Broker\FactoryInterface')->reveal(),
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );

        $configurator->setExtras([]);
        $input = $this->getUserInput([], $configurator);

        $this->assertSame(
            [
                'retry_key_pattern' => 'retry_%attempt%s',
                'retry_attempts' => 3,
                'retry_log_levels_map' => [],
                'retry_fail_log_levels_map' => [],
            ],
            $configurator->resolveOptions($input)
        );
    }

    public function testItIsDisablable()
    {
        $configurator = new RetryProcessorConfigurator(
            'Swarrot\Processor\Retry\RetryProcessor',
            $this->prophesize('Swarrot\SwarrotBundle\Broker\FactoryInterface')->reveal(),
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );
        $input = $this->getUserInput(['--no-retry' => true], $configurator);

        $configurator->resolveOptions($input);
        $this->assertFalse($configurator->isEnabled());
    }

    public function testItCanReturnsAValidProcessor()
    {
        $stubLogger = $this->prophesize('Psr\Log\LoggerInterface')->reveal();
        $stubMessagePublisher = $this->prophesize('Swarrot\Broker\MessagePublisher\MessagePublisherInterface')->reveal();
        $mockFactory = $this->prophesize('Swarrot\SwarrotBundle\Broker\FactoryInterface');
        $dummyQueue = uniqid();
        $dummyConnection = uniqid();

        $mockFactory->getMessagePublisher('retry', $dummyConnection)
            ->shouldBeCalled()
            ->willReturn($stubMessagePublisher);

        $configurator = new RetryProcessorConfigurator(
            'Swarrot\Processor\Retry\RetryProcessor',
            $mockFactory->reveal(),
            $stubLogger
        );

        $processor = $this->createProcessor($configurator, ['queue' => $dummyQueue, 'connection' => $dummyConnection]);

        $this->assertInstanceOf('Swarrot\Processor\Retry\RetryProcessor', $processor);
    }
}
