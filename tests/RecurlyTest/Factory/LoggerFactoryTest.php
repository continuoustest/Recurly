<?php
namespace RecurlyTest\Factory;

use Recurly\Factory\LoggerFactory;
use Zend\ServiceManager\ServiceManager;

class LoggerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateService()
    {
        $logger = $this->getMockBuilder('Zend\Log\Logger')
            ->disableOriginalConstructor()
            ->getMock();

        $serviceManager = new ServiceManager();
        $serviceManager
            ->setService('Recurly\ModuleConfig', [
                'notification' => [
                    'logger' => 'Zend\Log\Logger',
                ],
            ])
            ->setService('Zend\Log\Logger', $logger);

        $factory = new LoggerFactory();

        $logger = $factory->createService($serviceManager);
        $this->assertInstanceOf('Zend\Log\Logger', $logger);
    }

    /**
     * @expectedException \Recurly\Exception\InvalidArgumentException
     */
    public function testCreateServiceWithWrongLoggerService()
    {
        $serviceManager = new ServiceManager();
        $serviceManager
            ->setService('Recurly\ModuleConfig', [
                'notification' => [
                    'logger' => 'Foo\Log\Logger',
                ],
            ])
            ->setService('Foo\Log\Logger', new \stdClass());

        $factory = new LoggerFactory();

        $factory->createService($serviceManager);
    }
}