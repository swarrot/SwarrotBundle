<?php

namespace Swarrot\SwarrotBundle\Tests\DependencyInjection;

use Swarrot\SwarrotBundle\DependencyInjection\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_is_initializable()
    {
        $this->assertInstanceOf(
            'Swarrot\SwarrotBundle\DependencyInjection\Configuration',
            new Configuration(false)
        );
    }
}
