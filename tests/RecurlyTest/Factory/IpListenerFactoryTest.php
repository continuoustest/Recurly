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
        $serviceManager = new ServiceManager();
        $serviceManager->setService('Config', array(
            'recurly' => array(
                'notification' => array(
                    'ip_checking' => array(
                        'white_list' => array(),
                    ),
                ),
            ),
        ));

        $listener = $this->factory->createService($serviceManager);
        $this->assertInstanceOf('Recurly\Listener\IpListener', $listener);
    }
}