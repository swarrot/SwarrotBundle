<?php

namespace Swarrot\SwarrotBundle\Tests\Command;

use Swarrot\SwarrotBundle\Command\SwarrotCommand;

class SwarrotCommandTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_is_initializable()
    {
        $processor = $this->prophesize('Swarrot\Processor\ProcessorInterface');

        $command = new SwarrotCommand('foobar', 'foobar', $processor->reveal(), array(), array());
        $this->assertInstanceOf('Swarrot\SwarrotBundle\Command\SwarrotCommand', $command);
    }
}
