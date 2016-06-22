<?php

namespace Swarrot\SwarrotBundle\Tests\Processor\MaxMessages;

use Swarrot\SwarrotBundle\Processor\MaxMessages\MaxMessagesProcessorConfigurator;
use Swarrot\SwarrotBundle\Tests\Processor\ProcessorConfiguratorTestCase;

class MaxMessagesProcessorConfiguratorTest extends ProcessorConfiguratorTestCase
{
    public function test_it_is_initializable()
    {
        $configurator = new MaxMessagesProcessorConfigurator(
            'Swarrot\Processor\MaxMessages\MaxMessagesProcessor',
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );
        $this->assertInstanceOf(
            'Swarrot\SwarrotBundle\Processor\MaxMessages\MaxMessagesProcessorConfigurator',
            $configurator
        );
    }

    public function test_it_used_default_extra()
    {
        $configurator = new MaxMessagesProcessorConfigurator(
            'Swarrot\Processor\MaxMessages\MaxMessagesProcessor',
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );
        $configurator->setExtras(['max_messages' => 100]);
        $input = $this->getUserInput([], $configurator);

        $this->assertSame(['max_messages' => 100], $configurator->resolveOptions($input));
    }

    public function test_it_used_user_input()
    {
        $configurator = new MaxMessagesProcessorConfigurator(
            'Swarrot\Processor\MaxMessages\MaxMessagesProcessor',
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );

        $configurator->setExtras([]);
        $input = $this->getUserInput(['--max-messages' => 200], $configurator);

        $this->assertSame(['max_messages' => 200], $configurator->resolveOptions($input));
    }

    public function test_it_used_default_value()
    {
        $configurator = new MaxMessagesProcessorConfigurator(
            'Swarrot\Processor\MaxMessages\MaxMessagesProcessor',
            $this->prophesize('Psr\Log\LoggerInterface')->reveal()
        );

        $configurator->setExtras([]);
        $input = $this->getUserInput([], $configurator);

        $this->assertSame(['max_messages' => 300], $configurator->resolveOptions($input));
    }

    public function test_it_can_returns_a_valid_processor()
    {
        $stubLogger = $this->prophesize('Psr\Log\LoggerInterface')->reveal();

        $configurator = new MaxMessagesProcessorConfigurator(
            'Swarrot\Processor\MaxMessages\MaxMessagesProcessor',
            $stubLogger
        );

        $processor = $this->createProcessor($configurator);

        $this->assertInstanceOf('Swarrot\Processor\MaxMessages\MaxMessagesProcessor', $processor);
    }
}
