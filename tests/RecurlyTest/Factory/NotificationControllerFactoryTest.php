<?php
namespace RecurlyTest\Factory;

use Recurly\Factory\NotificationControllerFactory;
use Recurly\Receiver;
use Zend\ServiceManager\ServiceManager;

class NotificationControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NotificationControllerFactory
     */
    protected $factory;

    public function setUp()
    {
        $this->factory = new NotificationControllerFactory();
    }

    public function testCreateService()
    {
        $serviceLocator = $this->getMockBuilder('Zend\ServiceManager\ServiceLocatorInterface')
            ->setMethods(array('getServiceLocator', 'get', 'has'))
            ->getMock();
        
        $serviceManager = new ServiceManager();
        $serviceManager->setService('Recurly\Receiver', new Receiver());

        $serviceLocator
            ->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($serviceManager));

        $controller = $this->factory->createService($serviceLocator);
        $this->assertInstanceOf('Recurly\Controller\NotificationController', $controller);
    }
}