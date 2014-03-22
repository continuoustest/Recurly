<?php
return array(
    'recurly' => array(
        'notification' => array(
            'ip_checking' => array(
                'enable' => true,
                'white_list' => array(
                    '75.98.92.96', '75.98.92.97', '75.98.92.98', '75.98.92.99', '75.98.92.100', '75.98.92.101',
                    '75.98.92.102', '75.98.92.103', '75.98.92.104', '75.98.92.105', '75.98.92.106', '75.98.92.107',
                    '75.98.92.108', '75.98.92.109', '75.98.92.110', '75.98.92.111',
                ),
            ),
            'authentication' => array(
                'enable' => false,
                'auth_adapter' => array(
                    'config' => array(
                        'accept_schemes' => 'basic',
                        'realm'          => 'MyApp Site',
                        'digest_domains' => '/recurly/notification',
                        'nonce_timeout'  => 3600,
                    ),
                    'passwd_file'  => __DIR__ . '/../config/passwd.txt',
                ),
            ),
        ),
    ),

    'controllers' => array(
        'factories' => array(
            'Recurly\Controller\Notification' => 'Recurly\Factory\NotificationControllerFactory',
        ),
    ),

    'service_manager' => array(
        'invokables' => array(
            'Recurly\Listener\ErrorListener' => 'Recurly\Listener\ErrorListener',
            'Recurly\Notification\Handler'   => 'Recurly\Notification\Handler',
        ),
        'factories' => array(
            'Recurly\AuthenticationAdapter'           => 'Recurly\Factory\AuthenticationAdapterFactory',
            'Recurly\Listener\AuthenticationListener' => 'Recurly\Factory\AuthenticationListenerFactory',
            'Recurly\Listener\IpListener'             => 'Recurly\Factory\IpListenerFactory',
        ),
    ),

    'router' => array(
        'routes' => array(
            'recurly' => array(
                'type'    => 'Literal',
                'priority' => 1000,
                'options' => array(
                    'route'    => '/recurly',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Recurly\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'notification' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/notification',
                            'defaults' => array(
                                'controller' => 'notification',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);