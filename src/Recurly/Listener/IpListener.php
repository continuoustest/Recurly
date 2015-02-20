<?php
namespace Recurly\Listener;

use VectorFace\Whip\Whip;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\MvcEvent;

class IpListener extends AbstractAuthorizationListener
{
    /**
     * @var Whip
     */
    protected $whip;

    /**
     * @param Whip $whip
     */
    public function __construct(Whip $whip)
    {
        $this->whip = $whip;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, [$this, 'onResult'], -99);
    }

    /**
     * {@inheritDoc}
     */
    public function onResult(MvcEvent $event)
    {
        parent::onResult($event);

        if ($event->isError()) {
            if ($this->logger) {
                $this->logger->info(sprintf(
                    'Unauthorized ip address "%s" attempted to push Recurly notification.',
                    $this->whip->getIpAddress()
                ));
            }

            $event->getResponse()->setStatusCode(HttpResponse::STATUS_CODE_403);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function isGranted(MvcEvent $event)
    {
        return false !== $this->whip->getValidIpAddress();
    }
}