<?php
namespace Recurly\Factory;

use Recurly\Controller\NotificationController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class NotificationControllerFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return NotificationController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $controller = new NotificationController();

        $receiver = $serviceLocator->getServiceLocator()->get('Recurly\Receiver');
        $controller->setReceiver($receiver);

        return $controller;
    }
}