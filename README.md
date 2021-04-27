# SwarrotBundle

[![Latest Stable Version](https://poser.pugx.org/swarrot/swarrot-bundle/v/stable.png)](https://packagist.org/packages/swarrot/swarrot-bundle)
[![Latest Unstable Version](https://poser.pugx.org/swarrot/swarrot-bundle/v/unstable.svg)](https://packagist.org/packages/swarrot/swarrot-bundle)

A bundle to use Swarrot inside your Symfony application.

## Installation

The recommended way to install this bundle is through
[Composer](http://getcomposer.org/). Just run:

```bash
composer require swarrot/swarrot-bundle
```

Register the bundle in the kernel of your application:

```php
// app/AppKernel.php
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
    provider: pecl # pecl or amqp_lib (require php-amqplib/php-amqplib)
    default_connection: rabbitmq
    default_command: swarrot.command.base # Swarrot\SwarrotBundle\Command\SwarrotCommand
    logger: logger # logger or channel logger like monolog.logger.[my_channel]
    connections:
        rabbitmq:
            url: "amqp://%rabbitmq_login%:%rabbitmq_password%@%rabbitmq_host%:%rabbitmq_port%/%rabbitmq_vhost%"
    consumers:
        my_consumer:
            processor: my_consumer.processor.service # Symfony service id implementing Swarrot\Processor\ProcessorInterface
            middleware_stack: # order matters
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

                 # - configurator: swarrot.processor.services_resetter

            extras:
                poll_interval: 500000
    messages_types:
        my_publisher:
            connection: rabbitmq # use the default connection by default
            exchange: my_exchange
            routing_key: my_routing_key
```

## Publishing a message

First step is to retrieve the Swarrot publisher service from your controller.

```php
$messagePublisher = $this->get('swarrot.publisher');
```

After that, you need to prepare your message with the
[Message](https://github.com/swarrot/swarrot/blob/master/src/Swarrot/Broker/Message.php)
class.

```php
use Swarrot\Broker\Message;

$message = new Message('"My first message with the awesome swarrot lib :)"');
```

Then you can publish a new message into a predefined configuration
(`connection`, `exchange`, `routing_key`, etc.) from your `message_types`.

```php
$messagePublisher->publish('my_publisher', $message);
```

When publishing a message, you can override the `message_types` configuration
by passing a third argument:

```php
$messagePublisher->publish('my_publisher', $message, array(
    'exchange'    => 'my_new_echange',
    'connection'  => 'my_second_connection',
    'routing_key' => 'my_new_routing_key'
));
```

## Consuming a message

Swarrot will automatically create one command per consumer defined in your
configuration. These command need the queue name to consume as first argument.
You can also use a named connection as second argument if you don't want to use
the default one.

```bash
app/console swarrot:consume:my_consumer queue_name [connection_name]
```

Your consumer (```my_consumer.processor.service```) must implements ```Swarrot\Processor\ProcessorInterface```

```php
use Swarrot\Processor\ProcessorInterface;

class MyProcessor implements ProcessorInterface
{
    public function process(Message $message, array $options)
    {
        var_dump($message->getBody()); // "My first message with the awesome swarrot lib :)"
    }
}
```

Your processor will also be decorated automatically by all processors listed in the
`middleware_stack` section. The order matters.

All these processors are configurable.
You can add an `extras` key on each configurator definition in your `config.yml`.
Take a look at [the configuration reference](#configuration-reference) to see
available extras for existing Configurators.

You can also use options of the command line:

* **--poll-interval** [default: 500000]: Change the polling interval when no message found in broker
* **--requeue-on-error (-r)**: Re-queue the message in the same queue if an error occurred.
* **--no-catch (-C)**: Disable the ExceptionCatcher processor (available only if the processor is in the stack)
* **--max-execution-time (-t)** [default: 300]: Configure the MaxExecutionTime processor (available only if the processor is in the stack)
* **--max-messages (-m)** [default: 300]: Configure the MaxMessages processor (available only if the processor is in the stack)
* **--no-retry (-R)**: Disable the Retry processor (available only if the processor is in the stack)

Default values will be overriden by your `config.yml` and usage of options will
override default config values.

Run your command with `-h` to have the full list of options.

Note that you can define one or more _aliases_ for this command using the `command_alias` configuration:

```yaml
swarrot:
    consumers:
        my_consumer:
            command_alias: 'my:super:commmand'
```

Thus allowing you to consume messages using a more appropriate wording:

```bash
app/console my:super:command queue_name [connection_name]
```

## Implementing your own Provider

If you want to implement your own provider (like Redis), you first have to
implement the `Swarrot\SwarrotBundle\Broker\FactoryInterface`.
Then, you can register it along with the others services and tag it with
`swarrot.provider_factory`.

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

Now you can tell Swarrot to use it in the `config.yml` file.

```yaml
swarrot:
  provider: app.swarrot.custom_provider_factory
```

or with the alias

```yaml
swarrot:
  provider: redis
```

## Using a custom processor

If you want to use a custom processor, you need two things. The `Processor`
itself and a `ProcessorConfigurator`.
For the `Processor`, you can refer to the [`swarrot/swarrot`
documentation](https://github.com/swarrot/swarrot/#create-your-own-processor).
For the `ConfigurationProcessor`, you need to implement the
`ProcessorConfiguratorInterface` and to register it as an abstract service,
like this:
```
services:
  my_own_processor_configurator_service_id:
    abstract: true
    class: MyProject\MyOwnProcessorConfigurator
```
Once done, just add it to the middleware stack of your consumer:

```yaml
middleware_stack:
  - configurator: swarrot.processor.signal_handler
  - configurator: my_own_processor_configurator_service_id
```

As usual, take care of the order of your `middleware_stack`.

## Running your tests without publishing

If you use Swarrot, you may not want to actually publish messages when in test
environment for example. You can use the `BlackholePublisher` to achieve this.

Simply override the `swarrot.publisher.class` parameter in the DIC with the
`Swarrot\SwarrotBundle\Broker\BlackholePublisher` class, by updating
`config_test.yml` for instance:

```yaml
parameters:
    swarrot.publisher.class: Swarrot\SwarrotBundle\Broker\BlackholePublisher
```

## Broker configuration

This bundle goal is to deal with message consuming, not to deal with your
broker configuration. We don't want to mix the infrastructure logic with the
consuming one.

If you're looking for a tool to configure your broker, take a look at
[odolbeau/rabbit-mq-admin-toolkit](https://github.com/odolbeau/rabbit-mq-admin-toolkit).

## License

This bundle is released under the MIT License. See the bundled LICENSE file for
details.
