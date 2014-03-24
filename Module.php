<?php

namespace Recurly;

use Recurly_Client;
use Recurly_js;

class Module
{
    public function onBootstrap($e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();
        $config = $serviceManager->get('Recurly\ModuleConfig');
        
        if (empty($config['subdomain']) || empty($config['api_key'])) {
            return;
        }

        Recurly_Client::$subdomain = $config['subdomain'];
        Recurly_Client::$apiKey = $config['api_key'];

        if (isset($config['private_key'])) {
            Recurly_js::$privateKey = $config['private_key'];
        }

        $target = $e->getTarget();
        
        /* @var $eventManager  \Zend\EventManager\EventManager */
        $eventManager = $target->getEventManager();

        $notificationConfig = $config['notification'];

        if ($notificationConfig['ip_checking']['enabled']) {
            $ipListener = $target->getServiceManager()->get('Recurly\Listener\IpListener');
            $eventManager->attach($ipListener);
        }

        if ($notificationConfig['authentication']['enabled']) {
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