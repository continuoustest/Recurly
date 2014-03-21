<?php
return array(
    'recurly' => array(
        'notification' => array(
            'ip_addresses' => array(
                '75.98.92.96', '75.98.92.97', '75.98.92.98', '75.98.92.99', '75.98.92.100', '75.98.92.101',
                '75.98.92.102', '75.98.92.103', '75.98.92.104', '75.98.92.105', '75.98.92.106', '75.98.92.107',
                '75.98.92.108', '75.98.92.109', '75.98.92.110', '75.98.92.111',
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
            'Recurly\Receiver'               => 'Recurly\Receiver',
        ),
        'factories' => array(
            'Recurly\Listener\IpListener' => 'Recurly\Factory\IpListenerFactory',
        ),
    ),

    'router' => array(
        'routes' => array(
            'recurly' => array(
                'type'    => 'Literal',
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
                                'controller' => 'Notification',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);