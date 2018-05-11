<?php 
namespace User;

// Needed for routing
use Zend\Router\Http\Literal;

return [
    'service_manager' => [
        'aliases' => [
            Model\UserListInterface::class => Model\UserDbList::class,
            Model\UserWriteInterface::class => Model\UserDbWrite::class,            
        ],
        'factories' => [
            Model\UserDbList::class => Factory\UserDbListFactory::class,
            Model\UserDbWrite::class => Factory\UserDbWriteFactory::class,            
        ],
    ],
    // Controller Configuration
    'controllers' => [
        'factories' => [
            Controller\ListController::class => Factory\ListControllerFactory::class,
            Controller\WriteController::class => Factory\WriteControllerFactory::class,
            Controller\AuthController::class => Factory\AuthControllerFactory::class,            
        ],
    ],
    'view_manager' => [
        // DO NOT forget to add your proper module name !!
        'template_path_stack' => [
            'user' => __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
    'router' => [
        'routes' => [
            'login' => [
                // Define a "literal" route type:
                'type' => Literal::class,
                // Configure the route itself
                'options' => [
                    // Listen to "/login" as uri:
                    'route' => '/login',
                    // Define default controller and action to be called when
                    // this route is matched
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'login',
                    ],
                ],
            ],
            'logout' => [
                // Define a "literal" route type:
                'type' => Literal::class,
                // Configure the route itself
                'options' => [
                    // Listen to "/logout" as uri:
                    'route' => '/logout',
                    // Define default controller and action to be called when
                    // this route is matched
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'logout',
                    ],
                ],
            ],
            'me' => [
                // Define a "literal" route type:
                'type' => Literal::class,
                // Configure the route itself
                'options' => [
                    // Listen to "/user" as uri:
                    'route' => '/me',
                    // Define default controller and action to be called when
                    // this route is matched
                    'defaults' => [
                        'controller' => Controller\ListController::class,
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    '/' => [ // Listen to "/me/" as uri with the forward slash at the end
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/',
                            'defaults' => [
                                'controller' => Controller\ListController::class,
                                'action' => 'profile',
                            ],
                        ],
                    ],
                    'profile' => [ // Listen to "/me/profile" as uri with the forward slash at the end
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/profile',
                            'defaults' => [
                                'controller' => Controller\ListController::class,
                                'action' => 'profile',
                            ],
                        ],
                    ],
                ],
             ],
            'registro' => [
                // Define a "literal" route type:
                'type' => Literal::class,
                // Configure the route itself
                'options' => [
                    // Listen to "/login" as uri:
                    'route' => '/registro',
                    // Define default controller and action to be called when
                    // this route is matched
                    'defaults' => [
                        'controller' => Controller\WriteController::class,
                        'action'     => 'register',
                    ],
                ],
            ],
            'registro-ok' => [
                // Define a "literal" route type:
                'type' => Literal::class,
                // Configure the route itself
                'options' => [
                    // Listen to "/registro/confirmacion" as uri:
                    'route' => '/registro-ok',
                    // Define default controller and action to be called when
                    // this route is matched
                    'defaults' => [
                        'controller' => Controller\WriteController::class,
                        'action'     => 'register-confirmation',
                    ],
                ],
            ],
            'reset-password' => [
                // Define a "literal" route type:
                'type' => Literal::class,
                // Configure the route itself
                'options' => [
                    // Listen to "/reset-password" as uri:
                    'route' => '/reset-password',
                    // Define default controller and action to be called when
                    // this route is matched
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'reset-password',
                    ],
                ],
            ],
            'forbidden' => [
                // Define a "literal" route type:
                'type' => Literal::class,
                // Configure the route itself
                'options' => [
                    // Listen to "/forbidden" as uri:
                    'route' => '/forbidden',
                    // Define default controller and action to be called when
                    // this route is matched
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'forbidden',
                    ],
                ],
            ],
            'user' => [
                // Define a "literal" route type:
                'type' => Literal::class,
                // Configure the route itself
                'options' => [
                    // Listen to "/user" as uri:
                    'route' => '/user',
                    // Define default controller and action to be called when
                    // this route is matched
                    'defaults' => [
                        'controller' => Controller\ListController::class,
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    '/' => [ // Listen to "/user/" as uri with the forward slash at the end
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/',
                            'defaults' => [
                                'controller' => Controller\ListController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'setadmin' => [
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/setadmin',
                            'defaults' => [
                                'controller' => Controller\WriteController::class,
                                'action' => 'set-admin',
                            ],
                        ],
                    ],
                    'unsetadmin' => [
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/unsetadmin',
                            'defaults' => [
                                'controller' => Controller\WriteController::class,
                                'action' => 'unset-admin',
                            ],
                        ],
                    ],
                    'setactive' => [
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/setactive',
                            'defaults' => [
                                'controller' => Controller\WriteController::class,
                                'action' => 'set-active',
                            ],
                        ],
                    ],
                    'unsetactive' => [
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/unsetactive',
                            'defaults' => [
                                'controller' => Controller\WriteController::class,
                                'action' => 'unset-active',
                            ],
                        ],
                    ],
                ],
            ],            
        ],
    ],
];