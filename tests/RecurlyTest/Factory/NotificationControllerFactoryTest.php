<?php
namespace RecurlyTest\Factory;

use Recurly\Factory\NotificationControllerFactory;

class NotificationControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateService()
    {
        $notificationHandler = $this->getMock('Recurly\Notification\Handler');

        $serviceManager = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceManager
            ->expects($this->once())
            ->method('get')
            ->with('Recurly\Notification\Handler')
            ->will($this->returnValue($notificationHandler));

        $controllerPluginManager = $this->getMock('Zend\ServiceManager\AbstractPluginManager');
        $controllerPluginManager
            ->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($serviceManager));

        $factory = new NotificationControllerFactory();

        $controller = $factory->createService($controllerPluginManager);
        $this->assertInstanceOf('Recurly\Controller\NotificationController', $controller);
    }
}