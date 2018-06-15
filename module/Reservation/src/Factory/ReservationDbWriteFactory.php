<?php
namespace Reservation\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Reservation\Model\ReservationDbWrite;

class ReservationDbWriteFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null )
    {
        return new ReservationDbWrite( $container->get('Db\WriteAdapter') );        
    }
}