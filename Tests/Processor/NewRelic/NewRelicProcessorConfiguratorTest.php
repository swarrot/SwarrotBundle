<?php

namespace Swarrot\SwarrotBundle\Tests\Processor\NewRelic;

use Swarrot\SwarrotBundle\Processor\NewRelic\NewRelicProcessorConfigurator;
use Swarrot\SwarrotBundle\Tests\Processor\ProcessorConfiguratorTestCase;

class NewRelicProcessorConfiguratorTest extends ProcessorConfiguratorTestCase
{
    public function test_it_is_initializable()
    {
        $configurator = new NewRelicProcessorConfigurator();
        $this->assertInstanceOf(
            'Swarrot\SwarrotBundle\Processor\NewRelic\NewRelicProcessorConfigurator',
            $configurator
        );
    }

    public function test_it_resolves_options()
    {
        $configurator = new NewRelicProcessorConfigurator();
        $configurator->setExtras([]);
        $input = $this->getUserInput([], $configurator);

        $this->assertSame([], $configurator->resolveOptions($input));
    }

    public function test_it_can_returns_a_valid_processor()
    {
        $configurator = new NewRelicProcessorConfigurator();

        $processor = $this->createProcessor($configurator, []);

        $this->assertInstanceOf('Swarrot\Processor\NewRelic\NewRelicProcessor', $processor);
    }
}
