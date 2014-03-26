<?php
namespace Recurly\Factory;

use Recurly\Exception;
use Zend\Log\Logger;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LoggerFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Logger
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Recurly\ModuleConfig');
        
        /* @var $logger Logger */
        $logger = $serviceLocator->get($config['notification']['logger']);

        if (! $logger instanceof Logger) {
            throw new Exception\InvalidArgumentException(
                '`logger` option of Recurly module must be an instance or extend Zend\Log\Logger class.'
            );
        }
        
        if (count($logger->getWriters()) == 0) {
            $logger->addWriter('null');
        }

        return $logger;
    }
}