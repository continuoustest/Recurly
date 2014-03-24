<?php
namespace RecurlyTest\Factory;

use Recurly\Factory\ModuleConfigFactory;
use Zend\ServiceManager\ServiceManager;

class ModuleConfigFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ModuleConfigFactory
     */
    protected $factory;

    public function setUp()
    {
        $this->factory = new ModuleConfigFactory();
    }

    public function testCreateService()
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService('Config', array(
            'recurly' => array(),
        ));

        $config = $this->factory->createService($serviceManager);
        $this->assertInternalType('array', $config);
    }

    /**
     * @expectedException \Recurly\Exception\RuntimeException
     */
    public function testCreateServiceWithoutRecurlyConfigKey()
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService('Config', array());

        $config = $this->factory->createService($serviceManager);
        $this->assertInternalType('array', $config);
    }
}