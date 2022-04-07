<?php

namespace Swarrot\SwarrotBundle\Broker;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPSSLConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Swarrot\Broker\MessageProvider\MessageProviderInterface;
use Swarrot\Broker\MessageProvider\PhpAmqpLibMessageProvider;
use Swarrot\Broker\MessagePublisher\MessagePublisherInterface;
use Swarrot\Broker\MessagePublisher\PhpAmqpLibMessagePublisher;

class AmqpLibFactory implements FactoryInterface
{
    use UrlParserTrait;

    /** @var bool */
    protected $publisherConfirms;
    /** @var float */
    protected $timeout;
    /** @var array */
    protected $connections = [];
    /** @var array<AMQPChannel> */
    protected $channels = [];
    /** @var array */
    protected $messageProviders = [];
    /** @var array */
    protected $messagePublishers = [];

    public function __construct(bool $publisherConfirms = false, float $timeout = 0.0)
    {
        $this->publisherConfirms = $publisherConfirms;
        $this->timeout = $timeout;
    }

    /**
     * {@inheritdoc}
     */
    public function addConnection(string $name, array $connection): void
    {
        if (!empty($connection['url'])) {
            $params = $this->parseUrl($connection['url']);
            $connection = array_merge($connection, $params);
        }

        $this->connections[$name] = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageProvider(string $name, string $connection): MessageProviderInterface
    {
        if (!isset($this->messageProviders[$connection][$name])) {
            if (!isset($this->messageProviders[$connection])) {
                $this->messageProviders[$connection] = [];
            }

            $channel = $this->getChannel($connection);

            $this->messageProviders[$connection][$name] = new PhpAmqpLibMessageProvider($channel, $name);
        }

        return $this->messageProviders[$connection][$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getMessagePublisher(string $name, string $connection): MessagePublisherInterface
    {
        if (!isset($this->messagePublishers[$connection][$name])) {
            if (!isset($this->messagePublishers[$connection])) {
                $this->messagePublishers[$connection] = [];
            }

            $channel = $this->getChannel($connection);

            $this->messagePublishers[$connection][$name] = new PhpAmqpLibMessagePublisher($channel, $name, $this->publisherConfirms, $this->timeout);
        }

        return $this->messagePublishers[$connection][$name];
    }

    /**
     * Return the AMQPChannel of the given connection.
     */
    public function getChannel(string $connection): AMQPChannel
    {
        if (isset($this->channels[$connection])) {
            return $this->channels[$connection];
        }

        if (!isset($this->connections[$connection])) {
            throw new \InvalidArgumentException(sprintf('Unknown connection "%s". Available: [%s]', $connection, implode(', ', array_keys($this->connections))));
        }

        if (isset($this->connections[$connection]['ssl']) && $this->connections[$connection]['ssl']) {
            if (empty($this->connections[$connection]['ssl_options'])) {
                $ssl_opts = [
                    'verify_peer' => true,
                ];
            } else {
                $ssl_opts = [];
                foreach ($this->connections[$connection]['ssl_options'] as $key => $value) {
                    if (!empty($value)) {
                        $ssl_opts[$key] = $value;
                    }
                }
            }

            $conn = new AMQPSSLConnection(
                $this->connections[$connection]['host'],
                $this->connections[$connection]['port'],
                $this->connections[$connection]['login'],
                $this->connections[$connection]['password'],
                $this->connections[$connection]['vhost'],
                $ssl_opts
            );
        } else {
            $conn = new AMQPStreamConnection(
                $this->connections[$connection]['host'],
                $this->connections[$connection]['port'],
                $this->connections[$connection]['login'],
                $this->connections[$connection]['password'],
                $this->connections[$connection]['vhost']
            );
        }

        $this->channels[$connection] = $conn->channel();

        return $this->channels[$connection];
    }
}
