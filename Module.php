<?php

namespace Recurly;

use Recurly_Client;
use Recurly_js;

class Module
{
    public function onBootstrap($e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();
        $config = $serviceManager->get('Config');

        if (empty($config['recurly'])) {
            return;
        }
        
        $options = $config['recurly'];
        if (empty($options['subdomain']) || empty($options['api_key'])) {
            return;
        }

        Recurly_Client::$subdomain = $options['subdomain'];
        Recurly_Client::$apiKey = $options['api_key'];

        if (isset($options['private_key'])) {
            Recurly_js::$privateKey = $options['private_key'];
        }

        $target = $e->getTarget();
        
        /* @var $eventManager  \Zend\EventManager\EventManager */
        $eventManager = $target->getEventManager();

        $notificationConfig = $config['recurly']['notification'];

        if ($notificationConfig['ip_checking']['enable']) {
            $ipListener = $target->getServiceManager()->get('Recurly\Listener\IpListener');
            $eventManager->attach($ipListener);
        }

        if ($notificationConfig['authentication']['enable']) {
            $authenticationListener = $target->getServiceManager()->get('Recurly\Listener\AuthenticationListener');
            $eventManager->attach($authenticationListener);
        }

        $errorListener = $target->getServiceManager()->get('Recurly\Listener\ErrorListener');
        $eventManager->attach($errorListener);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

}