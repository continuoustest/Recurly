<?php
namespace RecurlyTest\Factory;

use Recurly\Factory\ModuleConfigFactory;
use Zend\ServiceManager\ServiceManager;

class ModuleConfigFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateService()
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService('Config', [
            'recurly' => [],
        ]);

        $factory = new ModuleConfigFactory();

        $config = $factory->createService($serviceManager);
        $this->assertInternalType('array', $config);
    }

    /**
     * @expectedException \Recurly\Exception\RuntimeException
     */
    public function testCreateServiceWithoutRecurlyConfigKey()
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService('Config', []);

        $factory = new ModuleConfigFactory();

        $config = $factory->createService($serviceManager);
        $this->assertInternalType('array', $config);
    }
}