<?php
namespace Reservation;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

return [
    'service_manager' => [
        'aliases' => [
            Model\ReservationListInterface::class => Model\ReservationDbList::class,
            Model\ReservationWriteInterface::class => Model\ReservationDbWrite::class,            
        ],
        'factories' => [
            Model\ReservationDbList::class => Factory\ReservationDbListFactory::class,
            Model\ReservationDbWrite::class => Factory\ReservationDbWriteFactory::class,
        ],
    ],
    // Controller Configuration
    'controllers' => [
        'factories' => [
            Controller\ListController::class => Factory\ListControllerFactory::class,
            Controller\WriteController::class => Factory\WriteControllerFactory::class,            
        ],
    ],
    'view_manager' => [
        // DO NOT forget to add your proper module name !!
        'template_path_stack' => [
            'reservation' => __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
    'router' => [
        'routes' => [
            'reserva' => [
                'type' => Segment::class,
                // Configure the route itself
                'options' => [
                    // Listen to "/reserva" as uri:
                    'route' => '/reserva[/:date]',
                    // Define default controller and action to be called when
                    // this route is matched
                    'defaults' => [
                        'controller' => Controller\ListController::class,
                        'action'     => 'index',
                    ],
                    'constraints' => [
                        'date' => '\d{2}\-\d{2}\-\d{4}', // something like "01-01-2018"
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    '/' => [ // Listen to "/reserva/" as uri with the forward slash at the end
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/',
                            'defaults' => [
                                'controller' => Controller\ListController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'guarda' => [ // Listen to /reserva/guarda
                        'type' => Literal::class, // Data comes from a POST
                        'options' => [
                            'route' => '/guarda',
                            'defaults' => [
                                'controller' => Controller\WriteController::class,
                                'action' => 'event-save',
                            ],
                        ],
                    ],
                    'crea' => [ // Listen to /reserva/crea/d-m-Y/roomId/hour e.g. /reserva/crea/12-06-2018/15/8
                        'type' => Segment::class, // Data comes from ROUTE
                        // COnfigure the route itself
                        'options' => [
                            // Listen to /reserva/crea/d-m-Y/roomId/hour
                            'route' => '/crea/:date/:roomId/:hour',
                            'defaults' => [
                                'controller' => Controller\WriteController::class,
                                'action'     => 'event-create',
                            ],
                            'constraints' => [
                                'date' => '\d{2}\-\d{2}\-\d{4}', // something like "01-01-2018"
                                'roomId' => '[0-9]*', // accept numbers only
                                'hour' => '[0-9]*', // accept numbers only
                            ],
                        ],                                                
                    ],
                 ], // End children's routes for /reserva/
            ],
            'evento' => [
                'type' => Segment::class,
                // Configure the route itself
                'options' => [
                    // Listen to "/evento/id" as uri:
                    'route' => '/evento/:id',
                    // Define default controller and action to be called when
                    // this route is matched
                    'defaults' => [
                        'controller' => Controller\ListController::class,
                        'action'     => 'event-details',
                    ],
                    'constraints' => [
                        'id' => '[0-9]*', // accept numbers only
                    ],
                ],
            ],
        ],
    ],
];