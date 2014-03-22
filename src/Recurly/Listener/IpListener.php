<?php
namespace Recurly\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;

class IpListener extends AbstractAuthorizationListener
{
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
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onResult'), -99);
    }

    /**
     * @param  MvcEvent $event
     * @return bool
     */
    public function isGranted(MvcEvent $event)
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $clientIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $clientIp = $_SERVER['REMOTE_ADDR'];
        }

        return in_array($clientIp, $this->ipAddresses);
    }
}