<?php
namespace Recurly\Factory;

use Recurly\Listener\IpListener;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class IpListenerFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return IpListener
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $whip = $serviceLocator->get('Recurly\Whip');

        $listener = new IpListener($whip);

        $logger = $serviceLocator->get('Recurly\Logger');
        $listener->setLogger($logger);

        return $listener;
    }
}