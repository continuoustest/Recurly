<?php
namespace Recurly\Factory;

use VectorFace\Whip\Whip;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class WhipFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Whip
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Recurly\ModuleConfig');

        $recurlyWhitelist = $config['notification']['security']['ip_checking']['white_list'];

        return new Whip(Whip::ALL_METHODS, [
            Whip::REMOTE_ADDR => $recurlyWhitelist,
        ]);
    }
}