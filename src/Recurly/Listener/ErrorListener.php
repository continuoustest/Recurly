<?php
namespace Recurly\Listener;

use Recurly\Exception\UnauthorizedExceptionInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\MvcEvent;

class ErrorListener extends AbstractListenerAggregate
{
    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onError'));
    }
    
    /**
     * @param  MvcEvent $event
     * @return void
     */
    public function onError(MvcEvent $event)
    {
        // Do nothing if no error or if response is not HTTP response
        if (!($exception = $event->getParam('exception') instanceof UnauthorizedExceptionInterface)
            || !($response = $event->getResponse() instanceof HttpResponse)
        ) {
            return;
        }

        $response = $event->getResponse() ?: new HttpResponse();
        $response->setStatusCode(HttpResponse::STATUS_CODE_403);

        $event->setResponse($response);
    }
}