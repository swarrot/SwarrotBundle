<?php

namespace Swarrot\SwarrotBundle\Tests\Broker;

use Swarrot\SwarrotBundle\Tests\TestCase;
use Swarrot\SwarrotBundle\Broker\PeclFactory;

class PeclFactoryTest extends TestCase
{
    public function test_it_is_initilizable()
    {
        $factory = new PeclFactory();
        $this->assertInstanceOf('Swarrot\SwarrotBundle\Broker\PeclFactory', $factory);
    }
}
