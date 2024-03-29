<?php
namespace RecurlyTest\Factory;

use Recurly\Factory\AuthenticationListenerFactory;
use Zend\ServiceManager\ServiceManager;

class AuthenticationListenerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AuthenticationListenerFactory
     */
    protected $factory;

    public function setUp()
    {
        $this->factory = new AuthenticationListenerFactory();
    }

    public function testCreateService()
    {
        $authAdapter = $this->getMockBuilder('Zend\Authentication\Adapter\Http')
            ->disableOriginalConstructor()
            ->getMock();
        
        $logger = $this->getMockBuilder('Zend\Log\Logger')
            ->disableOriginalConstructor()
            ->getMock();

        $serviceManager = new ServiceManager();
        $serviceManager
            ->setService('Recurly\AuthenticationAdapter', $authAdapter)
            ->setService('Recurly\Logger', $logger);

        $listener = $this->factory->createService($serviceManager);
        $this->assertInstanceOf('Recurly\Listener\AuthenticationListener', $listener);
    }
}