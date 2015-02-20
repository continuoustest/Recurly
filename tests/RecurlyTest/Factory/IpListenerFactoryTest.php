<?php
namespace RecurlyTest\Factory;

use Recurly\Factory\IpListenerFactory;
use Zend\ServiceManager\ServiceManager;

class IpListenerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateService()
    {
        $whip   = $this->getMock('VectorFace\Whip\Whip');
        $logger = $this->getMock('Zend\Log\LoggerInterface');

        $serviceManager = new ServiceManager();
        $serviceManager
            ->setService('Recurly\Whip',   $whip)
            ->setService('Recurly\Logger', $logger);

        $factory = new IpListenerFactory();

        $listener = $factory->createService($serviceManager);
        $this->assertInstanceOf('Recurly\Listener\IpListener', $listener);
    }
}