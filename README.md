# SwarrotBundle

[![Build Status](https://travis-ci.org/swarrot/SwarrotBundle.png)](https://travis-ci.org/swarrot/SwarrotBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/swarrot/SwarrotBundle/badges/quality-score.png?s=ec21025fb36203d8c7f39d4d68b647a58698816d)](https://scrutinizer-ci.com/g/swarrot/SwarrotBundle/)
[![Latest Stable Version](https://poser.pugx.org/swarrot/swarrot-bundle/v/stable.png)](https://packagist.org/packages/swarrot/swarrot-bundle)
[![Latest Unstable Version](https://poser.pugx.org/swarrot/swarrot-bundle/v/unstable.svg)](https://packagist.org/packages/swarrot/swarrot-bundle)

A bundle to use swarrot inside your Symfony2 application

## Installation

The recommended way to install this bundle is through
[Composer](http://getcomposer.org/). Require the `swarrot/swarrot-bundle`
package into your `composer.json` file:

```json
{
    "require": {
        "swarrot/swarrot-bundle": "@stable"
    }
}
```

**Protip:** you should browse the
[`swarrot/swarrot-bundle`](https://packagist.org/packages/swarrot/swarrot-bundle)
page to choose a stable version to use, avoid the `@stable` meta constraint.

## Configuration reference

```
swarrot:
    provider: pecl // pecl or amqp_lib
    default_connection: rabbitmq
    default_command: swarrot.command.base // Swarrot\SwarrotBundle\Command\SwarrotCommand
    connections:
        rabbitmq:
            host: "%rabbitmq_host%"
            port: "%rabbitmq_port%"
            login: "%rabbitmq_login%"
            password: "%rabbitmq_password%"
            vhost: '/'
    processors_stack:
        signal_handler: 'Swarrot\Processor\SignalHandler\SignalHandlerProcessor'
        ack: 'Swarrot\Processor\Ack\AckProcessor'
        max_messages: 'Swarrot\Processor\MaxMessages\MaxMessagesProcessor'
        retry: 'Swarrot\Processor\Retry\RetryProcessor'
        exception_catcher: 'Swarrot\Processor\ExceptionCatcher\ExceptionCatcherProcessor'
        max_execution_time: 'Swarrot\Processor\MaxExecutionTime\MaxExecutionTimeProcessor'
    consumers:
        my_consumer:
            processor: my_consumer.processor.service
            extras:
                retry_exchange: my_consumer_exchange
                retry_attempts: 3
                retry_routing_key_pattern: 'retry_%%attempt%%'
    messages_types:
        my_publisher:
            connection: rabbitmq // use the default connection by default
            exchange: my_exchange
            routing_key: my_routing_key

```

## Publish a message

First step is to retrieve the swarrot publisher service from your controller.

```php
$messagePublisher = $this->get('swarrot.publisher');
```

After you need to prepare your message with the
[Message](https://github.com/swarrot/swarrot/blob/master/src/Swarrot/Broker/Message.php)
class.

```php
use Swarrot\Broker\Message;

$message = new Message('"My first message with the awesome Swarrot lib :)"');
```

Then you can publish a new message into a predefined configuration (connection,
exchange, routing_key, etc.) from your `message_types`.

```php
$messagePublisher->publish('webhook.send', $message);
```

When publishing a message you can override the `message_types` configuration by
passing a third argument:

```php
$messagePublisher->publish('webhook.send', $message, array(
    'exchange'    => 'my_new_echange',
    'connection'  => 'my_second_connection',
    'routing_key' => 'my_new_routing_key'
);
```

## Consume a message

Swarrot will automatically create new commands according to your configuration.
This command need the queue name to consume as first argument. You can also use
a named connection as second argument if you don't want to use the default one.

```bash
app/console swarrot:consume:my_consumer_name queue_name [connection_name]
```

Your processor will automatically be decorated by all processors named in the
`processors_stack` section. No matthers the order you named your
processors, here is the default order:

* SignalHandler
* ExceptionCatcher
* MaxMessages
* MaxExecutionTime
* Ack
* Retry

All this processors are configurable with some options:

* **--poll-interval** [default: 500000]: Change the polling interval when no
  message found in broker
* **--requeue-on-error (-r)**: Re-queue the message in the same queue if an error
  occurred.
* **--no-catch (-C)**: Disable the ExceptionCatcher processor (available only if
  the processor is in the stack)
* **--max-execution-time (-t)** [default: 300]: Configure the MaxExecutionTime
  processor (available only if the processor is in the stack)
* **--max-messages (-m)** [default: 300]: Configure the MaxMessages processor
  (available only if the processor is in the stack)
* **--no-retry (-R)**: Disable the Retry processor (available only if the processor
  is in the stack)

## License

This bundle is released under the MIT License. See the bundled LICENSE file for
details.
