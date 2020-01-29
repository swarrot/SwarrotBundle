<?php

namespace Swarrot\SwarrotBundle\Tests\Processor\ServicesResetter;

use Swarrot\Processor\ServicesResetter\ServicesResetterProcessor;
use Swarrot\SwarrotBundle\Processor\ServicesResetter\ServicesResetterProcessorConfigurator;
use Swarrot\SwarrotBundle\Tests\Processor\ProcessorConfiguratorTestCase;

class ServicesResetterProcessorConfiguratorTest extends ProcessorConfiguratorTestCase
{
    protected function setUp(): void
    {
        if (!class_exists(ServicesResetterProcessor::class)) {
            $this->markTestSkipped('The ServicesResetter processor is not available');
        }
    }

    public function test_it_is_initializable()
    {
        $configurator = new ServicesResetterProcessorConfigurator(
            'Swarrot\Processor\ServicesResetter\ServicesResetterProcessor',
            $this->prophesize('Symfony\Contracts\Service\ResetInterface')->reveal()
        );
        $this->assertInstanceOf(
            'Swarrot\SwarrotBundle\Processor\ServicesResetter\ServicesResetterProcessorConfigurator',
            $configurator
        );
    }

    public function test_it_resolves_options()
    {
        $configurator = new ServicesResetterProcessorConfigurator(
            'Swarrot\Processor\ServicesResetter\ServicesResetterProcessor',
            $this->prophesize('Symfony\Contracts\Service\ResetInterface')->reveal()
        );
        $configurator->setExtras([]);
        $input = $this->getUserInput([], $configurator);

        $this->assertSame([], $configurator->resolveOptions($input));
    }

    public function test_it_is_disablable()
    {
        $configurator = new ServicesResetterProcessorConfigurator(
            'Swarrot\Processor\ServicesResetter\ServicesResetterProcessor',
            $this->prophesize('Symfony\Contracts\Service\ResetInterface')->reveal()
        );
        $input = $this->getUserInput(['--no-reset' => true], $configurator);

        $configurator->resolveOptions($input);
        $this->assertFalse($configurator->isEnabled());
    }

    public function test_it_can_returns_a_valid_processor()
    {
        $dummyResetter = $this->prophesize('Symfony\Contracts\Service\ResetInterface')->reveal();

        $configurator = new ServicesResetterProcessorConfigurator(
            'Swarrot\Processor\ServicesResetter\ServicesResetterProcessor',
            $dummyResetter
        );

        $processor = $this->createProcessor($configurator, []);

        $this->assertInstanceOf('Swarrot\Processor\ServicesResetter\ServicesResetterProcessor', $processor);
    }
}
