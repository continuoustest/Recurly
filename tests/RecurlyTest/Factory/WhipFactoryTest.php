<?php
namespace RecurlyTest\Factory;

use Recurly\Factory\WhipFactory;

class WhipFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateService()
    {
        $config = [
            'notification' => [
                'security' => [
                    'ip_checking' => [
                        'white_list' => [],
                    ],
                ],
            ],
        ];

        $serviceManager = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');

        $serviceManager
            ->expects($this->once())
            ->method('get')
            ->with('Recurly\ModuleConfig')
            ->will($this->returnValue($config));

        $factory = new WhipFactory();

        $listener = $factory->createService($serviceManager);
        $this->assertInstanceOf('VectorFace\Whip\Whip', $listener);
    }
}