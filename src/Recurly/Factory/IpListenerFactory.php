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
        $config = $serviceLocator->get('Recurly\ModuleConfig');
        
        $listener = new IpListener($config['notification']['security']['ip_checking']['white_list']);
        
        $logger = $serviceLocator->get('Recurly\Logger');
        $listener->setLogger($logger);
        
        return $listener;
    }
}