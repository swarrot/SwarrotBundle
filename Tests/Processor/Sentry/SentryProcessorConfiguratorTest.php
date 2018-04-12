<?php

namespace Swarrot\SwarrotBundle\Tests\Processor\Sentry;

use Swarrot\SwarrotBundle\Processor\Sentry\SentryProcessorConfigurator;
use Swarrot\SwarrotBundle\Tests\Processor\ProcessorConfiguratorTestCase;

class SentryProcessorConfiguratorTest extends ProcessorConfiguratorTestCase
{
    public function test_it_is_initializable()
    {
        $configurator = new SentryProcessorConfigurator(
            'Swarrot\Processor\Sentry\SentryProcessor',
            $this->prophesize('Raven_Client')->reveal()
        );
        $this->assertInstanceOf(
            'Swarrot\SwarrotBundle\Processor\Sentry\SentryProcessorConfigurator',
            $configurator
        );
    }

    public function test_it_resolves_options()
    {
        $configurator = new SentryProcessorConfigurator(
            'Swarrot\Processor\Sentry\SentryProcessor',
            $this->prophesize('Raven_Client')->reveal()
        );
        $configurator->setExtras([]);
        $input = $this->getUserInput([], $configurator);

        $this->assertSame([], $configurator->resolveOptions($input));
    }

    public function test_it_can_returns_a_valid_processor()
    {
        $configurator = new SentryProcessorConfigurator(
            'Swarrot\Processor\Sentry\SentryProcessor',
            $this->prophesize('Raven_Client')->reveal()
        );

        $processor = $this->createProcessor($configurator, []);

        $this->assertInstanceOf('Swarrot\Processor\Sentry\SentryProcessor', $processor);
    }
}
