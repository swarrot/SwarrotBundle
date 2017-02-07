# SwarrotBundle

[![Build Status](https://travis-ci.org/swarrot/SwarrotBundle.png)](https://travis-ci.org/swarrot/SwarrotBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/swarrot/SwarrotBundle/badges/quality-score.png?s=ec21025fb36203d8c7f39d4d68b647a58698816d)](https://scrutinizer-ci.com/g/swarrot/SwarrotBundle/)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/0a042607-3367-4057-b56d-c2d29c600c9a/mini.png)](https://insight.sensiolabs.com/projects/0a042607-3367-4057-b56d-c2d29c600c9a)
[![Latest Stable Version](https://poser.pugx.org/swarrot/swarrot-bundle/v/stable.png)](https://packagist.org/packages/swarrot/swarrot-bundle)
[![Latest Unstable Version](https://poser.pugx.org/swarrot/swarrot-bundle/v/unstable.svg)](https://packagist.org/packages/swarrot/swarrot-bundle)

A bundle to use swarrot inside your Symfony2 application

## Installation

The recommended way to install this bundle is through [Composer](http://getcomposer.org/). Just run:

```bash
composer require swarrot/swarrot-bundle
```

Update `app/AppKernel.php`:

```php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Swarrot\SwarrotBundle\SwarrotBundle(),
    );

    return $bundles;
}
```

## Configuration reference

```yaml
swarrot:
    provider: pecl # pecl or amqp_lib
    default_connection: rabbitmq
    default_command: swarrot.command.base # Swarrot\SwarrotBundle\Command\SwarrotCommand
    connections:
        rabbitmq:
            host: "%rabbitmq_host%"
            port: "%rabbitmq_port%"
            login: "%rabbitmq_login%"
            password: "%rabbitmq_password%"
            vhost: '/'
    consumers:
        my_consumer:
            processor: my_consumer.processor.service
            middleware_stack: # order matter
                 - configurator: swarrot.processor.signal_handler
                   # extras:
                   #     signal_handler_signals:
                   #         - SIGTERM
                   #         - SIGINT
                   #         - SIGQUIT
                 # - configurator: swarrot.processor.insomniac
                 - configurator: swarrot.processor.max_messages
                   # extras:
                   #     max_messages: 100
                 - configurator: swarrot.processor.max_execution_time
                   # extras:
                   #     max_execution_time: 300
                 - configurator: swarrot.processor.memory_limit
                   # extras:
                   #     memory_limit: null
                 - configurator: swarrot.processor.doctrine_connection
                   # extras:
                   #     doctrine_ping: true
                   #     doctrine_close_master: true
                 - configurator: swarrot.processor.doctrine_object_manager
                 - configurator: swarrot.processor.exception_catcher

                 - configurator: swarrot.processor.ack
                   # extras:
                   #     requeue_on_error: false
                 - configurator: swarrot.processor.retry
                   # extras:
                   #     retry_exchange: retry
                   #     retry_attempts: 3
                   #     retry_routing_key_pattern: 'retry_%%attempt%%'
                 # - configurator: swarrot.processor.new_relic
                 #   extras:
                 #       new_relic_app_name: ~
                 #       new_relic_license: ~
                 #       new_relic_transaction_name: ~

                 # - configurator: swarrot.processor.rpc_server
                 #   extras:
                 #       # Exchange on which rpc response will be published with `reply_to` as routing_key. 
                 #       # If not configured will publish on default exchange where routing_key is used to define receiving queue.
                 #       rpc_exchange: ~ 
                 # - configurator: swarrot.processor.rpc_client
                 #   extras:
                 #       rpc_client_correlation_id: ~
            extras:
                poll_interval: 500000
    messages_types:
        my_publisher:
            connection: rabbitmq # use the default connection by default
            exchange: my_exchange
            routing_key: my_routing_key
```

## Publish a message

First step is to retrieve the swarrot publisher service from your controller.

```php
$messagePublisher = $this->get('swarrot.publisher');
```

After you need to prepare your message with the [Message](https://github.com/swarrot/swarrot/blob/master/src/Swarrot/Broker/Message.php) class.

```php
use Swarrot\Broker\Message;

$message = new Message('"My first message with the awesome swarrot lib :)"');
```

Then you can publish a new message into a predefined configuration (connection, exchange, routing_key, etc.) from your `message_types`.

```php
$messagePublisher->publish('webhook.send', $message);
```

When publishing a message you can override the `message_types` configuration by passing a third argument:

```php
$messagePublisher->publish('webhook.send', $message, array(
    'exchange'    => 'my_new_echange',
    'connection'  => 'my_second_connection',
    'routing_key' => 'my_new_routing_key'
));
```

## Consume a message

Swarrot will automatically create new commands according to your configuration.
This command need the queue name to consume as first argument. You can also use a named connection as second argument if you don't want to use the default one.

```bash
app/console swarrot:consume:my_consumer_name queue_name [connection_name]
```

Your processor will automatically be decorated by all processors named in the `middleware_stack` section. The order matter.

All this processors are configurable.
You can add `extras` key on each configurator definition in your `config.yml`. Take a look at configuration reference to see available extras for existing Configurators.

You can also use options of the command line:

* **--poll-interval** [default: 500000]: Change the polling interval when no message found in broker
* **--requeue-on-error (-r)**: Re-queue the message in the same queue if an error occurred.
* **--no-catch (-C)**: Disable the ExceptionCatcher processor (available only if the processor is in the stack)
* **--max-execution-time (-t)** [default: 300]: Configure the MaxExecutionTime processor (available only if the processor is in the stack)
* **--max-messages (-m)** [default: 300]: Configure the MaxMessages processor (available only if the processor is in the stack)
* **--no-retry (-R)**: Disable the Retry processor (available only if the processor is in the stack)

Default values will be override by your `config.yml` and use of options will override defaut config values.

Run your command with `-h` to have the full list of options.

## Implementing your own Provider

If you want to implement your own provider (like Redis). First, you have to implements the `Swarrot\SwarrotBundle\Broker\FactoryInterface`.
Then, you can register it with along the others services and tag it with `swarrot.provider_factory`.

```yaml
services:
    app.swarrot.custom_provider_factory:
        class: AppBundle\Provider\CustomFactory
        tags:
            - {name: swarrot.provider_factory}
    app.swarrot.redis_provider_factory:
        class: AppBundle\Provider\RedisFactory
        tags:
            - {name: swarrot.provider_factory, alias: redis}
```

Now you can tell to swarrot to use it in the `config.yml` file.

```yaml
swarrot:
  provider: app.swarrot.custom_provider_factory
```

or with the alias

```yaml
swarrot:
  provider: redis
```

## How to use a custom processor

If you want to use a custom processor, you need two things. The Processor itself and a ProcessorConfigurator.
For the Processor, you can refer to the [`swarrot/swarrot` documentation](https://github.com/swarrot/swarrot/#create-your-own-processor).
For the ConfigurationProcessor, you need to implement the `ProcessorConfiguratorInterface` and to register it as a service. Once down, just add it to the middleware stack of your consumer:

```yaml
middleware_stack:
  - configurator: swarrot.processor.signal_handler
  - configurator: my_own_processor_configurator_service_id
```

As usual, take care of the order of your middleware_stack.

## Running your tests without publishing

If you use Swarrot you may not want to really publish  messages like in test environment for example. You can use the `BlackholePublisher` to achieve this.

Simply override the `swarrot.publisher.class` parameter in the DIC with the `Swarrot\SwarrotBundle\Broker\PublisherBlackhole` class.

Update `config_test.yml` for example:

```yaml
parameters:
    swarrot.publisher.class: Swarrot\SwarrotBundle\Broker\BlackholePublisher
```

## License

This bundle is released under the MIT License. See the bundled LICENSE file for details.
