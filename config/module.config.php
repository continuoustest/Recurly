<?php
return [
    'recurly' => [
        'notification' => [
            'logger' => 'Zend\Log\Logger',

            'security' => [
                'ip_checking' => [
                    'enabled' => true,
                    'white_list' => [
                        '74.201.212.175',
                        '64.74.141.175',
                        '75.98.92.102',
                        '74.201.212.0/24',
                        '64.74.141.0/24',
                        '75.98.92.96/28',
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
            'Recurly\Whip'                            => 'Recurly\Factory\WhipFactory',
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
