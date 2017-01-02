<?php

namespace Swarrot\SwarrotBundle\Tests\Processor\RPC;

use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use Swarrot\SwarrotBundle\Broker\FactoryInterface;
use Swarrot\SwarrotBundle\Processor\RPC\RpcServerProcessorConfigurator;
use Swarrot\SwarrotBundle\Tests\Processor\ProcessorConfiguratorTestCase;

class RpcServerProcessorConfiguratorTest extends ProcessorConfiguratorTestCase
{
    /**
     * @var FactoryInterface|ObjectProphecy
     */
    private $factory;

    /**
     * @var LoggerInterface|ObjectProphecy
     */
    private $logger;

    /**
     * @var RpcServerProcessorConfigurator
     */
    private $configurator;

    public function setUp()
    {
        $this->factory = $this->prophesize('Swarrot\SwarrotBundle\Broker\FactoryInterface');
        $this->logger = $this->prophesize('Psr\Log\LoggerInterface');

        $this->configurator = new RpcServerProcessorConfigurator(
            $this->factory->reveal(),
            $this->logger->reveal()
        );
    }

    public function test_it_is_initializable()
    {
        $this->assertInstanceOf('Swarrot\SwarrotBundle\Processor\RPC\RpcServerProcessorConfigurator', $this->configurator);
    }

    public function test_it_resolves_no_options()
    {
        $this->configurator->setExtras(['rpc_exchange' => 'exchange']);
        $input = $this->getUserInput([], $this->configurator);

        $this->assertSame([], $this->configurator->resolveOptions($input));
    }

    public function test_it_can_returns_a_valid_processor_with_default_empty_exchange()
    {
        $messagePublisher = $this->prophesize('Swarrot\Broker\MessagePublisher\MessagePublisherInterface');

        $this->factory->getMessagePublisher('', 'foo:bar@bar.com:5672')
            ->shouldBeCalled()
            ->willReturn($messagePublisher->reveal());

        $processor = $this->createProcessor($this->configurator, ['queue' => 'queue.rpc', 'connection' => 'foo:bar@bar.com:5672']);

        $this->assertInstanceOf('Swarrot\Processor\RPC\RpcServerProcessor', $processor);
    }

    public function test_it_can_returns_a_valid_processor_with_configurable_exchange()
    {
        $messagePublisher = $this->prophesize('Swarrot\Broker\MessagePublisher\MessagePublisherInterface');

        $this->configurator->setExtras(['rpc_exchange' => 'exchange.rpc']);

        $this->factory->getMessagePublisher('exchange.rpc', 'foo:bar@bar.com:5672')
            ->shouldBeCalled()
            ->willReturn($messagePublisher->reveal());

        $processor = $this->createProcessor($this->configurator, ['queue' => 'queue.rpc', 'connection' => 'foo:bar@bar.com:5672']);

        $this->assertInstanceOf('Swarrot\Processor\RPC\RpcServerProcessor', $processor);
    }
}
