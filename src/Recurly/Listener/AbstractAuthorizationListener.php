<?php
namespace Recurly\Listener;

use Recurly\Exception;
use Recurly\Module;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Http\Request as HttpRequest;
use Zend\Log\LoggerInterface;
use Zend\Mvc\MvcEvent;

abstract class AbstractAuthorizationListener implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = [];

    /**
     * LoggerInterface
     */
    protected $logger;

    /**
     * @param  LoggerInterface $logger
     * @return self
     */
    public function setLogger(LoggerInterface $logger)
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
        if ($matchedRouteName != Module::RECURLY_NOTIFICATION_ROUTE) {
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