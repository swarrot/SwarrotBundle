<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Swarrot\Processor\Ack\AckProcessor;
use Swarrot\Processor\Doctrine\ConnectionProcessor;
use Swarrot\Processor\Doctrine\ObjectManagerProcessor;
use Swarrot\Processor\ExceptionCatcher\ExceptionCatcherProcessor;
use Swarrot\Processor\MaxExecutionTime\MaxExecutionTimeProcessor;
use Swarrot\Processor\MaxMessages\MaxMessagesProcessor;
use Swarrot\Processor\Retry\RetryProcessor;
use Swarrot\Processor\ServicesResetter\ServicesResetterProcessor;
use Swarrot\Processor\SignalHandler\SignalHandlerProcessor;
use Swarrot\SwarrotBundle\Broker\AmqpLibFactory;
use Swarrot\SwarrotBundle\Broker\PeclFactory;
use Swarrot\SwarrotBundle\Broker\Publisher;
use Swarrot\SwarrotBundle\Command\SwarrotCommand;
use Swarrot\SwarrotBundle\Processor\Ack\AckProcessorConfigurator;
use Swarrot\SwarrotBundle\Processor\Doctrine\ConnectionProcessorConfigurator;
use Swarrot\SwarrotBundle\Processor\Doctrine\ObjectManagerProcessorConfigurator;
use Swarrot\SwarrotBundle\Processor\ExceptionCatcher\ExceptionCatcherProcessorConfigurator;
use Swarrot\SwarrotBundle\Processor\Insomniac\InsomniacProcessorConfigurator;
use Swarrot\SwarrotBundle\Processor\InstantRetry\InstantRetryProcessorConfigurator;
use Swarrot\SwarrotBundle\Processor\MaxExecutionTime\MaxExecutionTimeProcessorConfigurator;
use Swarrot\SwarrotBundle\Processor\MaxMessages\MaxMessagesProcessorConfigurator;
use Swarrot\SwarrotBundle\Processor\MemoryLimit\MemoryLimitProcessorConfigurator;
use Swarrot\SwarrotBundle\Processor\Retry\RetryProcessorConfigurator;
use Swarrot\SwarrotBundle\Processor\ServicesResetter\ServicesResetterProcessorConfigurator;
use Swarrot\SwarrotBundle\Processor\SignalHandler\SignalHandlerProcessorConfigurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('swarrot.factory.pecl.class', PeclFactory::class);
    $parameters->set('swarrot.factory.amqp_lib.class', AmqpLibFactory::class);
    $parameters->set('swarrot.command.base.class', SwarrotCommand::class);
    $parameters->set('swarrot.publisher.class', Publisher::class);

    $services->set('swarrot.factory.pecl', '%swarrot.factory.pecl.class%')
        ->args([
            service('swarrot.logger'),
            '%swarrot.publisher_confirm_enable%',
            '%swarrot.publisher_confirm_timeout%',
        ])
        ->tag('swarrot.provider_factory', ['alias' => 'pecl']);

    $services->set('swarrot.factory.amqp_lib', '%swarrot.factory.amqp_lib.class%')
        ->tag('swarrot.provider_factory', ['alias' => 'amqp_lib']);

    $services->set('swarrot.command.base', '%swarrot.command.base.class%')
        ->public()
        ->abstract()
        ->args([
            service('swarrot.factory.default'),
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ]);

    $services->set('swarrot.publisher', '%swarrot.publisher.class%')
        ->public()
        ->args([
            service('swarrot.factory.default'),
            service('event_dispatcher'),
            '%swarrot.messages_types%',
            service('swarrot.logger'),
        ]);

    $services->set('swarrot.processor.ack', AckProcessorConfigurator::class)
        ->args([
            AckProcessor::class,
            service('swarrot.factory.default'),
            service('swarrot.logger'),
        ]);

    $services->set('swarrot.processor.doctrine_connection', ConnectionProcessorConfigurator::class)
        ->args([
            ConnectionProcessor::class,
            service('doctrine')->ignoreOnInvalid(),
        ]);

    $services->set('swarrot.processor.doctrine_object_manager', ObjectManagerProcessorConfigurator::class)
        ->args([
            ObjectManagerProcessor::class,
            service('doctrine')->ignoreOnInvalid(),
        ]);

    $services->set('swarrot.processor.exception_catcher', ExceptionCatcherProcessorConfigurator::class)
        ->args([
            ExceptionCatcherProcessor::class,
            service('swarrot.logger'),
        ]);

    $services->set('swarrot.processor.max_execution_time', MaxExecutionTimeProcessorConfigurator::class)
        ->args([
            MaxExecutionTimeProcessor::class,
            service('swarrot.logger'),
        ]);

    $services->set('swarrot.processor.max_messages', MaxMessagesProcessorConfigurator::class)
        ->args([
            MaxMessagesProcessor::class,
            service('swarrot.logger'),
        ]);

    $services->set('swarrot.processor.retry', RetryProcessorConfigurator::class)
        ->args([
            RetryProcessor::class,
            service('swarrot.factory.default'),
            service('swarrot.logger'),
        ]);

    $services->set('swarrot.processor.signal_handler', SignalHandlerProcessorConfigurator::class)
        ->args([
            SignalHandlerProcessor::class,
            service('swarrot.logger'),
        ]);

    $services->set('swarrot.processor.insomniac', InsomniacProcessorConfigurator::class)
        ->args([service('swarrot.logger')]);

    $services->set('swarrot.processor.instant_retry', InstantRetryProcessorConfigurator::class)
        ->args([service('swarrot.logger')]);

    $services->set('swarrot.processor.memory_limit', MemoryLimitProcessorConfigurator::class)
        ->args([service('swarrot.logger')]);

    $services->set('swarrot.processor.services_resetter', ServicesResetterProcessorConfigurator::class)
        ->args([
            ServicesResetterProcessor::class,
            service('services_resetter')->ignoreOnInvalid(),
        ]);
};
