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
```
## License

This bundle is released under the MIT License. See the bundled LICENSE file for
details.
