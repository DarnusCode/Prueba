<?php
namespace User\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use User\Controller\AuthController;
use User\Model\UserListInterface;
use User\Model\UserWriteInterface;

class AuthControllerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Zend\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null)
    {        
        // Create the authentication adapter
        /** @var \Zend\Db\Adapter\Adapter $dbAdapter */
        $adapter = new \Zend\Authentication\Adapter\DbTable\CallbackCheckAdapter(
            $container->get('Db\ListAdapter'),
            'users', // Table name
            'user', // Identity column
            'password' // Credential column            
            );
        $adapter->getDbSelect()->where('active = 1'); // MUST be an active account
        // Create the storage adapter
        $storage = new \Zend\Authentication\Storage\Session();        
        // Finally create the service
        $authService = new \Zend\Authentication\AuthenticationService($storage, $adapter);
        // Create controller instance and inject objects
        return new AuthController( $authService, $container->get( UserListInterface::class ), $container->get( UserWriteInterface::class ) );        
    }
}