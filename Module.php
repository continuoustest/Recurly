<?php

namespace Recurly;

use Recurly_Client;
use Recurly_js;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $application = $e->getApplication();
        $serviceManager = $application->getServiceManager();
        $config = $serviceManager->get('Recurly\ModuleConfig');
        
        if (empty($config['subdomain']) || empty($config['api_key'])) {
            return;
        }

        Recurly_Client::$subdomain = $config['subdomain'];
        Recurly_Client::$apiKey = $config['api_key'];

        if (isset($config['private_key'])) {
            Recurly_js::$privateKey = $config['private_key'];
        }
        
        /* @var $eventManager  \Zend\EventManager\EventManager */
        $eventManager = $application->getEventManager();

        $notificationConfig = $config['notification'];

        if ($notificationConfig['security']['ip_checking']['enabled']) {
            $ipListener = $serviceManager->get('Recurly\Listener\IpListener');
            $eventManager->attach($ipListener);
        }

        if ($notificationConfig['security']['authentication']['enabled']) {
            $authenticationListener = $serviceManager->get('Recurly\Listener\AuthenticationListener');
            $eventManager->attach($authenticationListener);
        }

        $errorListener = $serviceManager->get('Recurly\Listener\ErrorListener');
        $eventManager->attach($errorListener);

        if (!empty($notificationConfig['listeners']) && is_array($notificationConfig['listeners'])) {
            $notificationHandler = $serviceManager->get('Recurly\Notification\Handler');

            foreach ($notificationConfig['listeners'] as $service) {
                $listener = $serviceManager->get($service);
                $notificationHandler->getEventManager()->attach($listener);
            }
        }
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