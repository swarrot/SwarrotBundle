<?php

namespace Swarrot\SwarrotBundle\DataCollector;

use Swarrot\SwarrotBundle\Event\MessagePublishedEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class SwarrotDataCollector extends DataCollector
{
    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, $exception = null)
    {
    }

    /**
     * onMessagePublished.
     */
    public function onMessagePublished(MessagePublishedEvent $event)
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
     * getMessages.
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->data;
    }

    /**
     * getNbMessages.
     *
     * @return int
     */
    public function getNbMessages()
    {
        return count($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'swarrot';
    }

    public function reset()
    {
        $this->data = [];
    }
}
