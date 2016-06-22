<?php

namespace Swarrot\SwarrotBundle\Tests\Processor\RPC;

use Swarrot\SwarrotBundle\Processor\RPC\RpcServerProcessorConfigurator;
use Swarrot\SwarrotBundle\Tests\Processor\ProcessorConfiguratorTestCase;

class RpcServerProcessorConfiguratorTest extends ProcessorConfiguratorTestCase
{
    public function test_it_is_initializable()
    {
        $configurator = new RpcServerProcessorConfigurator(
            $this->prophesize('Swarrot\SwarrotBundle\Broker\FactoryInterface')->reveal(),
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );
        $this->assertInstanceOf('Swarrot\SwarrotBundle\Processor\RPC\RpcServerProcessorConfigurator', $configurator);
    }

    public function test_it_resolves_options()
    {
        $configurator = new RpcServerProcessorConfigurator(
            $this->prophesize('Swarrot\SwarrotBundle\Broker\FactoryInterface')->reveal(),
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );
        $configurator->setExtras([]);
        $input = $this->getUserInput([], $configurator);

        $this->assertSame([], $configurator->resolveOptions($input));
    }

    public function test_it_can_returns_a_valid_processor()
    {
        $stubLogger = $this->prophesize('Psr\Log\LoggerInterface')->reveal();
        $stubMessagePublisher = $this->prophesize('Swarrot\Broker\MessagePublisher\MessagePublisherInterface')->reveal();
        $mockFactory = $this->prophesize('Swarrot\SwarrotBundle\Broker\FactoryInterface');
        $dummyQueue = uniqid();
        $dummyConnection = uniqid();

        $mockFactory->getMessagePublisher('retry', $dummyConnection)
            ->shouldBeCalled()
            ->willReturn($stubMessagePublisher);

        $configurator = new RpcServerProcessorConfigurator(
            $mockFactory->reveal(),
            $stubLogger
        );

        $processor = $this->createProcessor($configurator, ['queue' => $dummyQueue, 'connection' => $dummyConnection]);

        $this->assertInstanceOf('Swarrot\Processor\RPC\RpcServerProcessor', $processor);
    }
}
