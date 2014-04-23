<?php

namespace Swarrot\SwarrotBundle\Command;

use Swarrot\SwarrotBundle\Tests\TestCase;
use Swarrot\SwarrotBundle\Command\SwarrotCommand;

class SwarrotCommandTest extends TestCase
{
    public function test_it_is_initilizable()
    {
        $factory = $this->prophet->prophesize('Swarrot\SwarrotBundle\Broker\FactoryInterface');
        $processor = $this->prophet->prophesize('Swarrot\Processor\ProcessorInterface');

        $command = new SwarrotCommand($factory->reveal(), $processor->reveal(), 'foobar', 'foobar');
        $this->assertInstanceOf('Swarrot\SwarrotBundle\Command\SwarrotCommand', $command);
    }
}
