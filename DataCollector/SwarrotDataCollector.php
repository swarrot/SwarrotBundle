<?php

namespace Swarrot\SwarrotBundle\DataCollector;

use Swarrot\SwarrotBundle\Event\MessagePublishedEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\VarDumper\Cloner\Data;

class SwarrotDataCollector extends DataCollector
{
    /**
     * {@inheritdoc}
     *
     * @param \Throwable|\Exception $exception
     */
    public function collect(Request $request, Response $response, \Throwable $exception = null): void
    {
    }

    public function onMessagePublished(MessagePublishedEvent $event): void
    {
        $this->data[] = [
            'message_type' => $event->getMessageType(),
            'message' => $event->getMessage(),
            'connection' => $event->getConnection(),
            'exchange' => $event->getExchange(),
            'routing_key' => $event->getRoutingKey(),
        ];
    }

    /**
     * @return array|Data
     */
    public function getMessages()
    {
        return $this->data;
    }

    public function getNbMessages(): int
    {
        return count($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'swarrot';
    }

    public function reset(): void
    {
        $this->data = [];
    }
}
