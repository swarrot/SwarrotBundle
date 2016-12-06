<?php

namespace Swarrot\SwarrotBundle\Broker;

use Aws\Sqs\SqsClient;
use Swarrot\MessageProvider\SqsMessageProvider;
use Swarrot\Broker\MessageProvider\MessageProviderInterface;
use Swarrot\Broker\MessagePublisher\MessagePublisherInterface;
use Swarrot\SwarrotBundle\Broker\FactoryInterface;

/**
 * Class SqsFactory
 */
class SqsFactory implements FactoryInterface
{
    private $connections = array();
    private $messageProviders = array();
    private $messagePublishers = array();

    /**
     * {@inheritDoc}
     */
    public function addConnection($name, array $connection)
    {
        $this->connections[$name] = $connection;
    }

    /**
     * @param string $name       The name of the queue where the MessageProviderInterface will found messages
     * @param string $connection The name of the connection to use
     *
     * @return MessageProviderInterface
     */
    public function getMessageProvider($name, $connection)
    {
        if (!isset($this->messageProviders[$connection][$name])) {
            if (!isset($this->messageProviders[$connection])) {
                $this->messageProviders[$connection] = array();
            }

            $channel = $this->getChannel($connection);

            $this->messageProviders[$connection][$name] = new SqsMessageProvider($channel, $this->connections[$connection]['host'] . $name);
        }

        return $this->messageProviders[$connection][$name];
    }

    /**
     * getMessagePublisher.
     *
     * @param string $name       The name of the exchange where the MessagePublisher will publish
     * @param string $connection The name of the connection to use
     *
     * @return MessagePublisherInterface
     */
    public function getMessagePublisher($name, $connection)
    {
        throw new \Exception('Implement method getMessagePublisher');
    }

    /**
     * getChannel.
     *
     * @param string $connection
     *
     * @return SqsClient
     */
    private function getChannel($connection)
    {
        if (!isset($this->connections[$connection])) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown connection "%s". Available: [%s]',
                $connection,
                implode(', ', array_keys($this->connections))
            ));
        }

        return SqsClient::factory([
            'key' => $this->connections[$connection]['login'],
            'secret' => $this->connections[$connection]['password'],
            'region' => $this->connections[$connection]['region'],
        ]);
    }
}
