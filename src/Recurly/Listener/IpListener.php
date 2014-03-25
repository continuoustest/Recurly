<?php
namespace Recurly\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\Http\Response as HttpResponse;
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
     * @return void
     */
    public function onResult(MvcEvent $event)
    {
        parent::onResult($event);

        if (!$this->isGranted($event)) {
            if ($this->logger) {
                $this->logger->info(sprintf(
                    'Unauthorized ip address "%s" attempt to push Recurly notification.',
                    $this->getClientIpAddress()
                ));
            }

            $response = $event->getResponse();
            $response->setStatusCode(HttpResponse::STATUS_CODE_403);
        }
    }

    /**
     * @param  MvcEvent $event
     * @return bool
     */
    public function isGranted(MvcEvent $event)
    {
        if (!isset($this->isGranted)) {
            $clientIp = $this->getClientIpAddress();
            $this->isGranted = in_array($clientIp, $this->ipAddresses);
        }

        return $this->isGranted;
    }

    /**
     * @return string
     */
    protected function getClientIpAddress()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        return $_SERVER['REMOTE_ADDR'];
    }
}