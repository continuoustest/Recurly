<?php
namespace Recurly\Listener;

use Recurly\Exception;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Http\Request as HttpRequest;
use Zend\Log\Logger;
use Zend\Mvc\MvcEvent;

abstract class AbstractAuthorizationListener implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();
    
    /**
     * Logger
     */
    protected $logger;

    /**
     * @var bool
     */
    protected $isGranted;
    
    /**
     * @param  Logger $logger
     * @return self
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $callback) {
            if ($events->detach($callback)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * @param  MvcEvent $event
     * @return void
     */
    public function onResult(MvcEvent $event)
    {
        $request = $event->getRequest();

        if (!$request instanceof HttpRequest) {
            return;
        }

        $routeMatch = $event->getRouteMatch();        
        $matchedRouteName = $routeMatch->getMatchedRouteName();
        if ($matchedRouteName != 'recurly/notification') {
            return;
        }

        if ($this->isGranted($event)) {
            return;
        }

        $event->setError('unauthorized');
        $event->setParam('exception', new Exception\UnauthorizedException(
            'You are not authorized to access this resource'
        ));

        $event->stopPropagation(true);

        $application  = $event->getApplication();
        $eventManager = $application->getEventManager();

        $eventManager->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $event);
    }
}