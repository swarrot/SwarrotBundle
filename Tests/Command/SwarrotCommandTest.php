<?php

namespace Swarrot\SwarrotBundle\Tests\Command;

use Swarrot\SwarrotBundle\Tests\TestCase;
use Swarrot\SwarrotBundle\Command\SwarrotCommand;

class SwarrotCommandTest extends TestCase
{
    public function test_it_is_initilizable()
    {
        $processor = $this->prophet->prophesize('Swarrot\Processor\ProcessorInterface');

        $command = new SwarrotCommand('foobar', 'foobar', $processor->reveal(), array(), array());
        $this->assertInstanceOf('Swarrot\SwarrotBundle\Command\SwarrotCommand', $command);
    }
}
