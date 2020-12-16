<?php

namespace Swarrot\SwarrotBundle\Tests\Processor\Ack;

use Swarrot\SwarrotBundle\Processor\Ack\AckProcessorConfigurator;
use Swarrot\SwarrotBundle\Tests\Processor\ProcessorConfiguratorTestCase;

class AckProcessorConfiguratorTest extends ProcessorConfiguratorTestCase
{
    public function testItIsInitializable()
    {
        $configurator = new AckProcessorConfigurator(
            'Swarrot\Processor\Ack\AckProcessor',
            $this->prophesize('Swarrot\SwarrotBundle\Broker\FactoryInterface')->reveal(),
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );
        $this->assertInstanceOf('Swarrot\SwarrotBundle\Processor\Ack\AckProcessorConfigurator', $configurator);
    }

    public function testItUsedDefaultExtra()
    {
        $configurator = new AckProcessorConfigurator(
            'Swarrot\Processor\Ack\AckProcessor',
            $this->prophesize('Swarrot\SwarrotBundle\Broker\FactoryInterface')->reveal(),
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );
        $configurator->setExtras(['requeue_on_error' => true]);
        $input = $this->getUserInput([], $configurator);

        $this->assertSame(['requeue_on_error' => true], $configurator->resolveOptions($input));
    }

    public function testItUsedUserInput()
    {
        $configurator = new AckProcessorConfigurator(
            'Swarrot\Processor\Ack\AckProcessor',
            $this->prophesize('Swarrot\SwarrotBundle\Broker\FactoryInterface')->reveal(),
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );

        $configurator->setExtras([]);
        $input = $this->getUserInput(['--requeue-on-error' => true], $configurator);

        $this->assertSame(['requeue_on_error' => true], $configurator->resolveOptions($input));
    }

    public function testItUsedDefaultValue()
    {
        $configurator = new AckProcessorConfigurator(
            'Swarrot\Processor\Ack\AckProcessor',
            $this->prophesize('Swarrot\SwarrotBundle\Broker\FactoryInterface')->reveal(),
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );

        $configurator->setExtras([]);
        $input = $this->getUserInput([], $configurator);

        $this->assertSame(['requeue_on_error' => false], $configurator->resolveOptions($input));
    }

    public function testItIsDisablable()
    {
        $configurator = new AckProcessorConfigurator(
            'Swarrot\Processor\Ack\AckProcessor',
            $this->prophesize('Swarrot\SwarrotBundle\Broker\FactoryInterface')->reveal(),
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );
        $input = $this->getUserInput(['--no-ack' => true], $configurator);

        $configurator->resolveOptions($input);
        $this->assertFalse($configurator->isEnabled());
    }

    public function testItCanReturnsAValidProcessor()
    {
        $stubLogger = $this->prophesize('Psr\Log\LoggerInterface')->reveal();
        $stubMessageProvider = $this->prophesize('Swarrot\Broker\MessageProvider\MessageProviderInterface')->reveal();
        $mockFactory = $this->prophesize('Swarrot\SwarrotBundle\Broker\FactoryInterface');
        $dummyQueue = uniqid();
        $dummyConnection = uniqid();

        $mockFactory->getMessageProvider($dummyQueue, $dummyConnection)
            ->shouldBeCalled()
            ->willReturn($stubMessageProvider);

        $configurator = new AckProcessorConfigurator(
            'Swarrot\Processor\Ack\AckProcessor',
            $mockFactory->reveal(),
            $stubLogger
        );

        $processor = $this->createProcessor($configurator, ['queue' => $dummyQueue, 'connection' => $dummyConnection]);

        $this->assertInstanceOf('Swarrot\Processor\Ack\AckProcessor', $processor);
    }
}
