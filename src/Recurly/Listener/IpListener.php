<?php
namespace Recurly\Listener;

use Recurly\Exception;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Request as HttpRequest;
use Zend\Mvc\MvcEvent;

class IpListener
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * List of IPs to blacklist
     */
    protected $ipAddresses = array();

    /**
     * @param array $ipAddresses
     */
    public function __construct(array $ipAddresses)
    {
        $this->ipAddresses = $ipAddresses;
    }
    
    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onResult'), 100);
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

        $response = $event->getResponse();
        $response->setStatusCode(401);

        return $response;
    }

    /**
     * @param  MvcEvent $event
     * @return bool
     */
    public function isGranted(MvcEvent $event)
    {
        $request = $event->getRequest();

        if (!$request instanceof HttpRequest) {
            return true;
        }

        $routeMatch = $event->getRouteMatch();
        $matchedRouteName = $routeMatch->getMatchedRouteName();
        if ($matchedRouteName != 'recurly/notification') {
            return true;
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $clientIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $clientIp = $_SERVER['REMOTE_ADDR'];
        }

        return in_array($clientIp, $this->ipAddresses);
    }
}