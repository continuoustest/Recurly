<?php
namespace RecurlyTest\Factory;

use Recurly\Factory\AuthenticationAdapterFactory;
use Zend\ServiceManager\ServiceManager;

class AuthenticationAdapterFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateService()
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService('Recurly\ModuleConfig', [
            'notification' => [
                'security' => [
                    'authentication' => [
                        'auth_adapter' => [
                            'config' => [
                                'accept_schemes' => 'basic',
                                'realm'          => 'MyApp Site',
                            ],
                            'passwd_file'  => __DIR__ . '/_files/passwd.txt',
                        ],
                    ],
                ],
            ],
        ]);

        $factory = new AuthenticationAdapterFactory();

        $adapter = $factory->createService($serviceManager);
        $this->assertInstanceOf('Zend\Authentication\Adapter\Http', $adapter);
    }
}