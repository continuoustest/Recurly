<?php
namespace Recurly\Factory;

use Zend\Authentication\Adapter\Http as HttpAdapter;
use Zend\Authentication\Adapter\Http\FileResolver;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthenticationAdapterFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return HttpAdapter
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config         = $serviceLocator->get('Config');
        $authConfig     = $config['recurly']['notification']['auth_adapter'];
        $authAdapter    = new HttpAdapter($authConfig['config']);
        
        $basicResolver  = new FileResolver();
        $basicResolver->setFile($authConfig['passwd_file']);
        $authAdapter->setBasicResolver($basicResolver);

        return $authAdapter;
    }
}