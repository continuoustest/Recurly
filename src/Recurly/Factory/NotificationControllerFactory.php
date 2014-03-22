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

        $handler = $serviceLocator->getServiceLocator()->get('Recurly\Notification\Handler');
        $controller->setNotificationHandler($handler);

        return $controller;
    }
}