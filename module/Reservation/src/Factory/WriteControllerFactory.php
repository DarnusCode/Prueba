<?php
namespace Reservation\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Reservation\Controller\WriteController;
use Reservation\Model\ReservationListInterface;
use Reservation\Model\ReservationWriteInterface;

class WriteControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null){
        
        return new WriteController( 
            $container->get( ReservationListInterface::class ), 
            $container->get( ReservationWriteInterface::class) 
        );        
    }
}

