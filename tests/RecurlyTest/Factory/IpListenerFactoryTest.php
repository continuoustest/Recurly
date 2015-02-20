<?php
namespace RecurlyTest\Factory;

use Recurly\Factory\IpListenerFactory;
use Zend\ServiceManager\ServiceManager;

class IpListenerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IpListenerFactory
     */
    protected $factory;

    public function setUp()
    {
        $this->factory = new IpListenerFactory();
    }

    public function testCreateService()
    {
        $logger = $this->getMockBuilder('Zend\Log\Logger')
            ->disableOriginalConstructor()
            ->getMock();

        $serviceManager = new ServiceManager();
        $serviceManager
            ->setService('Recurly\ModuleConfig', [
                'notification' => [
                    'security' => [
                        'ip_checking' => [
                            'white_list' => [],
                        ],
                    ],
                ],
            ])
            ->setService('Recurly\Logger', $logger);

        $listener = $this->factory->createService($serviceManager);
        $this->assertInstanceOf('Recurly\Listener\IpListener', $listener);
    }
}