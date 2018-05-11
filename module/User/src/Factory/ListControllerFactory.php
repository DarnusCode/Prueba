<?php
namespace User\Factory;

// Include User Controller and Interface
use User\Controller\ListController;
use User\Model\UserListInterface;

// Implements Factory Interface so it can be called from module.config
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class ListControllerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Zend\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke( ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ListController( $container->get(UserListInterface::class) );        
    }

    
}

