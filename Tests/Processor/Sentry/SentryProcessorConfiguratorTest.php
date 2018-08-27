<?php

namespace Swarrot\SwarrotBundle\Tests\Processor\Sentry;

use Swarrot\Processor\Sentry\SentryProcessor;
use Swarrot\SwarrotBundle\Processor\Sentry\SentryProcessorConfigurator;
use Swarrot\SwarrotBundle\Tests\Processor\ProcessorConfiguratorTestCase;

class SentryProcessorConfiguratorTest extends ProcessorConfiguratorTestCase
{
    protected function setUp()
    {
        if (!class_exists(SentryProcessor::class)) {
            $this->markTestSkipped('The Sentry processor is not available');
        }
    }

    public function test_it_is_initializable()
    {
        $configurator = new SentryProcessorConfigurator(
            SentryProcessor::class,
            $this->prophesize('Raven_Client')->reveal()
        );
        $this->assertInstanceOf(
            SentryProcessorConfigurator::class,
            $configurator
        );
    }

    public function test_it_resolves_options()
    {
        $configurator = new SentryProcessorConfigurator(
            SentryProcessor::class,
            $this->prophesize('Raven_Client')->reveal()
        );
        $configurator->setExtras([]);
        $input = $this->getUserInput([], $configurator);

        $this->assertSame([], $configurator->resolveOptions($input));
    }

    public function test_it_can_returns_a_valid_processor()
    {
        $configurator = new SentryProcessorConfigurator(
            SentryProcessor::class,
            $this->prophesize('Raven_Client')->reveal()
        );

        $processor = $this->createProcessor($configurator, []);

        $this->assertInstanceOf(SentryProcessor::class, $processor);
    }
}
