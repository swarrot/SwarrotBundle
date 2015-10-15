<?php

namespace Swarrot\SwarrotBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swarrot\SwarrotBundle\Event\MessagePublishedEvent;

class SwarrotDataCollector extends DataCollector
{
    /**
     * {@inheritDoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
    }

    /**
     * onMessagePublished.
     *
     * @param MessagePublishedEvent $event
     */
    public function onMessagePublished(MessagePublishedEvent $event)
    {
        $this->data[] = array(
            'message_type' => $event->getMessageType(),
            'message' => $event->getMessage(),
            'connection' => $event->getConnection(),
            'exchange' => $event->getExchange(),
            'routing_key' => $event->getRoutingKey(),
        );
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
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'swarrot';
    }
}
