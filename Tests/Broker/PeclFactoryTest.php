<?php

namespace Swarrot\SwarrotBundle\Tests\Broker;

use Swarrot\SwarrotBundle\Broker\PeclFactory;

class PeclFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_is_initializable()
    {
        $factory = new PeclFactory();
        $this->assertInstanceOf('Swarrot\SwarrotBundle\Broker\PeclFactory', $factory);
    }
}
