<?php

namespace Swarrot\SwarrotBundle\Broker;

use Swarrot\Broker\MessageProvider\PeclPackageMessageProvider;

class PeclFactory implements FactoryInterface
{
    protected $connections = array();
    protected $defaultConnection;

    protected $messageProviders = array();

    /**
     * {@inheritDoc}
     */
    public function addConnection($name, array $connection)
    {
        $this->connections[$name] = $connection;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultConnection($name)
    {
        $this->defaultConnection = $name;
    }

    /**
     * {@inheritDoc}
     */
    public function getMessageProvider($name, $connection = null)
    {
        if (null === $connection) {
            $connection = $this->getDefaultConnection();
        }

        if (!isset($this->messageProviders[$connection][$name])) {
            if (!isset($this->messageProviders[$connection])) {
                $this->messageProviders[$connection] = array();
            }

            $this->messageProviders[$connection][$name] = new PeclPackageMessageProvider(
                new \AMQPQueue(
                    $this->getChannel($name, $connection)
                )
            );
        }

        return $this->messageProviders[$connection][$name];
    }

    /**
     * {@inheritDoc}
     */
    public function getMessagePublisher($name, $connection = null)
    {
    }

    /**
     * getChannel
     *
     * @param string $name
     * @param string $connection
     *
     * @throws \AMQPConnectionException
     *
     * @return \AMQPChannel
     */
    protected function getChannel($name, $connection)
    {
        if (isset($this->channels[$connection][$name])) {
            return $this->channels[$connection][$name];
        }

        if (!isset($this->connection[$name])) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown connection "%s". Available: [%s]',
                implode(', ', array_keys($this->connections))
            ));
        }

        if (!isset($this->channels[$connection])) {
            $this->channels[$connection] = array();
        }

        $conn = new \AMQPConnection($this->connection[$name]);
        $conn->connect();

        $this->channels[$connection][$name] = new \AMQPChannel($conn);

        return $this->channels[$connection][$name];
    }

    /**
     * getDefaultConnection
     *
     * @return string
     */
    protected function getDefaultConnection()
    {
        if (null === $this->defaultConnection) {
            $this->setDefaultConnection(key($this->connection));
        }

        return $this->defaultConnection;
    }
}
