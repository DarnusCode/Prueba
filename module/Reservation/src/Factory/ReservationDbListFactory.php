<?php
namespace Reservation\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Reservation\Model\ReservationDbList;

class ReservationDbListFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null ){
        
        // Get DB Adapter for read only operations
        return new ReservationDbList( $container->get('Db\ListAdapter') );
        
    }
}