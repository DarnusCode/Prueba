<?php
namespace User\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

// Needed when creanting an instance of UserZendDbSqlList object
use Zend\Db\Adapter\Adapter;
use User\Model\UserDbList;


/**
 * To handle more than one database adapters
 * https://docs.zendframework.com/tutorials/db-adapter/
 * 
 * Sometimes you may need multiple adapters. 
 * As an example, if you work with a cluster of databases, one may allow write operations, 
 * while another may be read-only.
 * 
 * Retrieving named adapters
 * 
 * Retrieve named adapters in your service factories just as you would another service:
 * 
 * function ($container) {
 *     return new SomeServiceObject($container->get('Db\ReadOnlyAdapter));
 * }
 * 
 * See line: return new UserZendDbSqlList( $container->get('Db\SlaveAdapter') ...
 * 
 * @author Julio_MOLINERO
 *
 */
class UserDbListFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Zend\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke( ContainerInterface $container, $requestedName, array $options = null)
    {         
        // Get DB Adapter for read only operations
        return new UserDbList( $container->get('Db\ListAdapter') );
        
    }    
}

