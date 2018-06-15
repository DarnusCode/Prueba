<?php
namespace Reservation\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Reservation\Controller\ListController;
use Reservation\Model\ReservationListInterface;
use User\Model\UserListInterface;

class ListControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null){
        return new ListController( 
            $container->get(ReservationListInterface::class),
            $container->get(UserListInterface::class)
         );        
    }
}

