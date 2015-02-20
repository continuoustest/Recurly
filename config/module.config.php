<?php
return [
    'recurly' => [
        'notification' => [
            'logger' => 'Zend\Log\Logger',

            'security' => [
                'ip_checking' => [
                    'enabled' => true,
                    'white_list' => [
                        '75.98.92.96', '75.98.92.97', '75.98.92.98', '75.98.92.99', '75.98.92.100', '75.98.92.101',
                        '75.98.92.102', '75.98.92.103', '75.98.92.104', '75.98.92.105', '75.98.92.106', '75.98.92.107',
                        '75.98.92.108', '75.98.92.109', '75.98.92.110', '75.98.92.111',
                    ],
                ],

                'authentication' => [
                    'enabled' => false,
                    'auth_adapter' => [
                        'config' => [
                            'accept_schemes' => 'basic',
                            'realm'          => 'MyApp Site',
                            'digest_domains' => '/recurly/push',
                            'nonce_timeout'  => 3600,
                        ],
                        'passwd_file'  => __DIR__ . '/../config/passwd.txt',
                    ],
                ],
            ],

            'listeners' => [],
        ],
    ],

    'controllers' => [
        'factories' => [
            'Recurly\Controller\Notification' => 'Recurly\Factory\NotificationControllerFactory',
        ],
    ],

    'service_manager' => [
        'invokables' => [
            'Recurly\Listener\ErrorListener' => 'Recurly\Listener\ErrorListener',
            'Recurly\Notification\Handler'   => 'Recurly\Notification\Handler',
            'Zend\Log\Logger'                => 'Zend\Log\Logger',
        ],
        'factories' => [
            'Recurly\ModuleConfig'                    => 'Recurly\Factory\ModuleConfigFactory',
            'Recurly\AuthenticationAdapter'           => 'Recurly\Factory\AuthenticationAdapterFactory',
            'Recurly\Logger'                          => 'Recurly\Factory\LoggerFactory',
            'Recurly\Listener\AuthenticationListener' => 'Recurly\Factory\AuthenticationListenerFactory',
            'Recurly\Listener\IpListener'             => 'Recurly\Factory\IpListenerFactory',
        ],
    ],

    'router' => [
        'routes' => [
            'recurly' => [
                'type'    => 'Literal',
                'priority' => 1000,
                'options' => [
                    'route'    => '/recurly',
                    'defaults' => [
                        '__NAMESPACE__' => 'Recurly\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ],
                    'constraints' => [
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'notification' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/push',
                            'defaults' => [
                                'controller' => 'notification',
                                'action'     => 'push',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
