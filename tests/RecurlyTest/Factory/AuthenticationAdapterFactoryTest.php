<?php
namespace RecurlyTest\Factory;

use Recurly\Factory\AuthenticationAdapterFactory;
use Zend\ServiceManager\ServiceManager;

class AuthenticationAdapterFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AuthenticationAdapterFactory
     */
    protected $factory;

    public function setUp()
    {
        $this->factory = new AuthenticationAdapterFactory();
    }

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

        $adapter = $this->factory->createService($serviceManager);
        $this->assertInstanceOf('Zend\Authentication\Adapter\Http', $adapter);
    }
}