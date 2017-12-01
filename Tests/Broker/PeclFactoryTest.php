<?php

namespace Swarrot\SwarrotBundle\Tests\Broker;

use Swarrot\SwarrotBundle\Broker\PeclFactory;
use PHPUnit\Framework\TestCase;

class PeclFactoryTest extends TestCase
{
    protected function setUp()
    {
        if (!class_exists('AMQPConnection')) {
            $this->markTestSkipped('The AMQP extension is not available');
        }
    }

    public function test_it_is_initializable()
    {
        $factory = new PeclFactory();
        $this->assertInstanceOf('Swarrot\SwarrotBundle\Broker\PeclFactory', $factory);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown connection "connection". Available: []
     */
    public function test_get_publisher_with_unknown_connection()
    {
        $logger = $this->prophesize('Psr\Log\LoggerInterface');
        $factory = new PeclFactory($logger->reveal());

        $factory->getMessagePublisher('exchange', 'connection');
    }

    public function test_get_publisher_with_known_connection()
    {
        $logger = $this->prophesize('Psr\Log\LoggerInterface');
        $factory = new PeclFactory($logger->reveal());
        $factory->addConnection('connection', ['vhost' => 'swarrot']);

        $publisher = $factory->getMessagePublisher('exchange', 'connection');
        $this->assertInstanceOf('Swarrot\Broker\MessagePublisher\PeclPackageMessagePublisher', $publisher);
    }
}
