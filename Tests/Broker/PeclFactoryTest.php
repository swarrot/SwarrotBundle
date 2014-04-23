<?php

namespace Swarrot\SwarrotBundle\Broker;

use Swarrot\SwarrotBundle\Tests\TestCase;

class PeclFactoryTest extends TestCase
{
    public function test_it_is_initilizable()
    {
        $factory = new PeclFactory();
        $this->assertInstanceOf('Swarrot\SwarrotBundle\Broker\PeclFactory', $factory);
    }
}
