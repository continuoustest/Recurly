<?php
namespace Recurly\Factory;

use Recurly\Listener\AuthenticationListener;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthenticationListenerFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return AuthenticationListener
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $authAdapter = $serviceLocator->get('Recurly\AuthenticationAdapter');
        
        $listener = new AuthenticationListener($authAdapter);
        
        $logger = $serviceLocator->get('Recurly\Logger');
        $listener->setLogger($logger);
        
        return $listener;
    }
}