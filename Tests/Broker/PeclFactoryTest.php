<?php

namespace Swarrot\SwarrotBundle\Tests\Broker;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Swarrot\SwarrotBundle\Broker\PeclFactory;

class PeclFactoryTest extends TestCase
{
    use ProphecyTrait;

    protected function setUp(): void
    {
        if (!class_exists('AMQPConnection')) {
            $this->markTestSkipped('The AMQP extension is not available');
        }
    }

    public function testItIsInitializable()
    {
        $factory = new PeclFactory();
        $this->assertInstanceOf('Swarrot\SwarrotBundle\Broker\PeclFactory', $factory);
    }

    public function testGetPublisherWithUnknownConnection()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown connection "connection". Available: []');

        $logger = $this->prophesize('Psr\Log\LoggerInterface');
        $factory = new PeclFactory($logger->reveal());

        $factory->getMessagePublisher('exchange', 'connection');
    }

    public function testGetPublisherWithKnownConnection()
    {
        $logger = $this->prophesize('Psr\Log\LoggerInterface');
        $factory = new PeclFactory($logger->reveal());
        $factory->addConnection('connection', ['vhost' => 'swarrot']);

        $publisher = $factory->getMessagePublisher('exchange', 'connection');
        $this->assertInstanceOf('Swarrot\Broker\MessagePublisher\PeclPackageMessagePublisher', $publisher);
    }

    public function testGetPublisherWithConnectionBuildFromUrl()
    {
        $url = 'amqp://localhost:5672/swarrot';

        $logger = $this->prophesize('Psr\Log\LoggerInterface');
        $factory = new PeclFactory($logger->reveal());

        $factory->addConnection('connection', ['url' => $url]);

        $publisher = $factory->getMessagePublisher('exchange', 'connection');
        $this->assertInstanceOf('Swarrot\Broker\MessagePublisher\PeclPackageMessagePublisher', $publisher);
    }

    public function testItThrowsAnExceptionIfTheUrlIsInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid connection URL given: "bloup"');

        $logger = $this->prophesize('Psr\Log\LoggerInterface');
        $factory = new PeclFactory($logger->reveal());

        $factory->addConnection('connection', ['url' => 'bloup']);
    }
}
