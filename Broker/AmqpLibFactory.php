<?php

namespace Swarrot\SwarrotBundle\Broker;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Connection\AMQPSSLConnection;
use Swarrot\Broker\MessageProvider\PhpAmqpLibMessageProvider;
use Swarrot\Broker\MessagePublisher\PhpAmqpLibMessagePublisher;

class AmqpLibFactory implements FactoryInterface
{
    use UrlParserTrait;

    protected $connections = array();
    protected $channels = array();
    protected $messageProviders = array();
    protected $messagePublishers = array();

    /**
     * {@inheritDoc}
     */
    public function addConnection($name, array $connection)
    {
        if (!empty($connection['url'])) {
            $params = $this->parseUrl($connection['url']);
            $connection = array_merge($connection, $params);
        }

        $this->connections[$name] = $connection;
    }

    /**
     * {@inheritDoc}
     */
    public function getMessageProvider($name, $connection)
    {
        if (!isset($this->messageProviders[$connection][$name])) {
            if (!isset($this->messageProviders[$connection])) {
                $this->messageProviders[$connection] = array();
            }

            $channel = $this->getChannel($connection);

            $this->messageProviders[$connection][$name] = new PhpAmqpLibMessageProvider($channel, $name);
        }

        return $this->messageProviders[$connection][$name];
    }

    /**
     * {@inheritDoc}
     */
    public function getMessagePublisher($name, $connection)
    {
        if (!isset($this->messagePublishers[$connection][$name])) {
            if (!isset($this->messagePublishers[$connection])) {
                $this->messagePublishers[$connection] = array();
            }

            $channel = $this->getChannel($connection);

            $this->messagePublishers[$connection][$name] = new PhpAmqpLibMessagePublisher($channel, $name);
        }

        return $this->messagePublishers[$connection][$name];
    }

    /**
     * Return the AMQPChannel of the given connection.
     *
     * @param string $connection
     *
     * @return AMQPChannel
     */
    public function getChannel($connection)
    {
        if (isset($this->channels[$connection])) {
            return $this->channels[$connection];
        }

        if (!isset($this->connections[$connection])) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown connection "%s". Available: [%s]',
                $connection,
                implode(', ', array_keys($this->connections))
            ));
        }

        if (!isset($this->channels[$connection])) {
            $this->channels[$connection] = array();
        }

        if (isset($this->connections[$connection]['ssl']) && $this->connections[$connection]['ssl']) {
            if (empty($this->connections[$connection]['ssl_options'])) {
                $ssl_opts = array(
                    'verify_peer' => true,
                );
            } else {
                $ssl_opts = array();
                foreach ($this->connections[$connection]['ssl_options'] as $key => $value) {
                    if (isset($value)) {
                        $ssl_opts[$key] = $value;
                    }
                }
            }

            if (!empty($this->connections[$connection]['link'])) {
                $conn = AMQPSSLConnection::create_connection(
                    $this->mapMultiConnectionsParam($this->connections[$connection]['link']),
                    ['ssl_options' => $ssl_opts]
                );
            } else {
                $conn = new AMQPSSLConnection(
                    $this->connections[$connection]['host'],
                    $this->connections[$connection]['port'],
                    $this->connections[$connection]['login'],
                    $this->connections[$connection]['password'],
                    $this->connections[$connection]['vhost'],
                    $ssl_opts
                );
            }
        } else {
            if (!empty($this->connections[$connection]['link'])) {
                $conn = AMQPConnection::create_connection(
                    $this->mapMultiConnectionsParam($this->connections[$connection]['link'])
                );
            } else {
                $conn = new AMQPConnection(
                    $this->connections[$connection]['host'],
                    $this->connections[$connection]['port'],
                    $this->connections[$connection]['login'],
                    $this->connections[$connection]['password'],
                    $this->connections[$connection]['vhost']
                );
            }
        }

        $this->channels[$connection] = $conn->channel();

        return $this->channels[$connection];
    }

    /**
     * @param array $connections
     *
     * @return array
     */
    private function mapMultiConnectionsParam(array $connections)
    {
        return array_map(
            function ($param) {
                return [
                    'host' => $param['host'],
                    'port' => $param['port'],
                    'user' => $param['login'],
                    'password' => $param['password'],
                    'vhost' => $param['vhost'],
                ];
            },
            $connections
        );
    }
}
