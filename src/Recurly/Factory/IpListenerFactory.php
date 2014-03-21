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
        $config = $serviceLocator->get('Config');

        return new IpListener($config['recurly']['notification']['ip_addresses']);
    }
}