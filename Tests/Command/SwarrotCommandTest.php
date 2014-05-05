<?php

namespace Swarrot\SwarrotBundle\Command;

use Swarrot\SwarrotBundle\Tests\TestCase;

class SwarrotCommandTest extends TestCase
{
    public function test_it_is_initilizable()
    {
        $processor = $this->prophet->prophesize('Swarrot\Processor\ProcessorInterface');

        $command = new SwarrotCommand('foobar', 'foobar', $processor->reveal(), array(), array());
        $this->assertInstanceOf('Swarrot\SwarrotBundle\Command\SwarrotCommand', $command);
    }
}
