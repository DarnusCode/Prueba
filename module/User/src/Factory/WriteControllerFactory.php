<?php
namespace User\Factory;

use User\Controller\WriteController;
use Zend\ServiceManager\Factory\FactoryInterface;
use User\Model\UserListInterface;
use User\Model\UserWriteInterface;

class WriteControllerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Zend\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null)
    {
        // Pass UserInterface and UserCommandInterface instances
        return new WriteController(
            $container->get( UserListInterface::class ),
            $container->get( UserWriteInterface::class )            
         );        
    }

    
}

