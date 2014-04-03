<?php
namespace Recurly\Factory;

use Zend\Authentication\Adapter\Http as AuthAdapter;
use Zend\Authentication\Adapter\Http\FileResolver;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthenticationAdapterFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return AuthAdapter
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config         = $serviceLocator->get('Recurly\ModuleConfig');
        $authConfig     = $config['notification']['security']['authentication']['auth_adapter'];
        $authAdapter    = new AuthAdapter($authConfig['config']);
        
        $basicResolver  = new FileResolver();
        $basicResolver->setFile($authConfig['passwd_file']);
        $authAdapter->setBasicResolver($basicResolver);

        return $authAdapter;
    }
}