<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Swarrot\SwarrotBundle\DataCollector\SwarrotDataCollector;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('swarrot.data_collector.class', SwarrotDataCollector::class);

    $services->set('swarrot.data_collector', '%swarrot.data_collector.class%')
        ->tag('data_collector', ['template' => '@Swarrot/Collector/collector.html.twig', 'id' => 'swarrot'])
        ->tag('kernel.event_listener', ['event' => 'swarrot.message_published', 'method' => 'onMessagePublished']);
};
