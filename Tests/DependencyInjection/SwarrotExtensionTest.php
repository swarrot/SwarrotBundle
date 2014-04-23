<?php

namespace Swarrot\SwarrotBundle\Tests\DependencyInjection;

use Swarrot\SwarrotBundle\Tests\TestCase;
use Swarrot\SwarrotBundle\DependencyInjection\SwarrotExtension;

class SwarrotExtensionTest extends TestCase
{
    public function test_it_is_initilizable()
    {
        $this->assertInstanceOf(
            'Swarrot\SwarrotBundle\DependencyInjection\SwarrotExtension',
            new SwarrotExtension()
        );
    }
}
