<?php

namespace Swarrot\SwarrotBundle\Tests\Processor\RPC;

use Swarrot\SwarrotBundle\Processor\RPC\RpcClientProcessorConfigurator;
use Swarrot\SwarrotBundle\Tests\Processor\ProcessorConfiguratorTestCase;

class RpcClientProcessorConfiguratorTest extends ProcessorConfiguratorTestCase
{
    public function test_it_is_initializable()
    {
        $configurator = new RpcClientProcessorConfigurator(
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );
        $this->assertInstanceOf(
            'Swarrot\SwarrotBundle\Processor\RPC\RpcClientProcessorConfigurator',
            $configurator
        );
    }

    public function test_it_resolves_options()
    {
        $configurator = new RpcClientProcessorConfigurator(
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );
        $configurator->setExtras([]);
        $input = $this->getUserInput([], $configurator);

        $this->assertSame([], $configurator->resolveOptions($input));
    }

    public function test_it_can_returns_a_valid_processor()
    {
        $dummyConnection = $this->prophesize('Psr\Log\LoggerInterface')->reveal();

        $configurator = new RpcClientProcessorConfigurator(
            $dummyConnection
        );

        $processor = $this->createProcessor($configurator, []);

        $this->assertInstanceOf('Swarrot\Processor\RPC\RpcClientProcessor', $processor);
    }
}
