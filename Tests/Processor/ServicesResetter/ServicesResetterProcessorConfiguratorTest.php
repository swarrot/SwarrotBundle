<?php

namespace Swarrot\SwarrotBundle\Tests\Processor\ServicesResetter;

use Swarrot\SwarrotBundle\Processor\ServicesResetter\ServicesResetterProcessorConfigurator;
use Swarrot\SwarrotBundle\Tests\Processor\ProcessorConfiguratorTestCase;
use Symfony\Contracts\Service\ResetInterface;

class ServicesResetterProcessorConfiguratorTest extends ProcessorConfiguratorTestCase
{
    protected function setUp(): void
    {
        if (!class_exists(ResetInterface::class)) {
            $this->markTestSkipped('The ServicesResetter processor is not available');
        }
    }

    public function testItIsInitializable()
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

    public function testItResolvesOptions()
    {
        $configurator = new ServicesResetterProcessorConfigurator(
            'Swarrot\Processor\ServicesResetter\ServicesResetterProcessor',
            $this->prophesize('Symfony\Contracts\Service\ResetInterface')->reveal()
        );
        $configurator->setExtras([]);
        $input = $this->getUserInput([], $configurator);

        $this->assertSame([], $configurator->resolveOptions($input));
    }

    public function testItIsDisablable()
    {
        $configurator = new ServicesResetterProcessorConfigurator(
            'Swarrot\Processor\ServicesResetter\ServicesResetterProcessor',
            $this->prophesize('Symfony\Contracts\Service\ResetInterface')->reveal()
        );
        $input = $this->getUserInput(['--no-reset' => true], $configurator);

        $configurator->resolveOptions($input);
        $this->assertFalse($configurator->isEnabled());
    }

    public function testItCanReturnsAValidProcessor()
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
