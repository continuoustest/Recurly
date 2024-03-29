<?php
namespace Recurly\Listener;

use Recurly\Exception;
use Recurly\Module;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\Http\Request as HttpRequest;
use Zend\Log\LoggerAwareTrait;
use Zend\Mvc\MvcEvent;

abstract class AbstractAuthorizationListener extends AbstractListenerAggregate
{
    use LoggerAwareTrait;

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

        $matchedRouteName = $event->getRouteMatch()->getMatchedRouteName();
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

        $application = $event->getApplication();
        $application->getEventManager()->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $event);
    }

    /**
     * @param  MvcEvent $event
     * @return bool
     */
    abstract protected function isGranted(MvcEvent $event);
}