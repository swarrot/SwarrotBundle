<?php

namespace Swarrot\SwarrotBundle\Tests\Broker;

use Swarrot\SwarrotBundle\Broker\PeclFactory;
use PHPUnit\Framework\TestCase;

class PeclFactoryTest extends TestCase
{
    protected function setUp(): void
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

    public function test_get_publisher_with_unknown_connection()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown connection "connection". Available: []');

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


    public function test_get_publisher_with_connection_build_from_url()
    {
        $url = 'amqp://localhost:5672/swarrot';

        $logger = $this->prophesize('Psr\Log\LoggerInterface');
        $factory = new PeclFactory($logger->reveal());

        $factory->addConnection('connection', ['url' => $url]);

        $publisher = $factory->getMessagePublisher('exchange', 'connection');
        $this->assertInstanceOf('Swarrot\Broker\MessagePublisher\PeclPackageMessagePublisher', $publisher);
    }

    public function test_it_throws_an_exception_if_the_url_is_invalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid connection URL given: "bloup"');

        $logger = $this->prophesize('Psr\Log\LoggerInterface');
        $factory = new PeclFactory($logger->reveal());

        $factory->addConnection('connection', ['url' => 'bloup']);
    }
}
