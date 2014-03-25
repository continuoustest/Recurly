<?php
namespace Recurly\Listener;

use Zend\Authentication\Adapter\Http as AuthAdapter;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\MvcEvent;

class AuthenticationListener extends AbstractAuthorizationListener
{
    /**
     * AuthAdapter
     */
    protected $authAdapter;

    /**
     * @param AuthAdapter $authAdapter
     */
    public function __construct(AuthAdapter $authAdapter)
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

        if (!$this->isGranted($event)) {
            if ($this->logger) {
                $this->logger->info('Failed authentication attempt to push Recurly notification.');
            }

            $response = $event->getResponse();
            $response->setStatusCode(HttpResponse::STATUS_CODE_401);
        }
    }

    /**
     * @param  MvcEvent $event
     * @return bool
     */
    public function isGranted(MvcEvent $event)
    {
        if (!isset($this->isGranted)) {
            $result = $this->authAdapter->authenticate();
            $this->isGranted = $result->isValid();
        }

        return $this->isGranted;
    }
}