<?php
namespace Recurly\Listener;

use Zend\Authentication\Adapter\Http as HttpAdapter;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\MvcEvent;

class AuthenticationListener
{
    /**
     * HttpAdapter
     */
    protected $authAdapter = array();

    /**
     * @param HttpAdapter $authAdapter
     */
    public function __construct(HttpAdapter $authAdapter)
    {
        $this->authAdapter = $authAdapter;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onResult'), -100);
    }

    /**
     * @param  MvcEvent $event
     * @return void
     */
    public function onResult(MvcEvent $event)
    {
        parent::onResult($event);

        $response = $event->getResponse();
        $response->setStatusCode(HttpResponse::STATUS_CODE_401);
    }

    /**
     * @param  MvcEvent $event
     * @return bool
     */
    public function isGranted(MvcEvent $event)
    {
        $result = $this->authAdapter->authenticate();

        return $result->isValid();
    }
}