<?php
namespace User\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;

// Needed when creanting an instance of UserZendDbSqlCommand object
use Zend\Db\Adapter\Adapter;
use User\Model\UserDbWrite;

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
 * See line: return new UserZendDbSqlCommand( $container->get('Db\MainAdapter') ...
 *
 * @author Julio_MOLINERO
 *
 */
class UserDbWriteFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Zend\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null)
    {
        // Get DB Adapter for writting operations
        return new UserDbWrite( $container->get('Db\WriteAdapter') );        
    }    
}

