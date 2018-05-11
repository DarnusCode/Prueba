<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

class Module
{
    const VERSION = '0.0.1';    

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
    /**
     * Get what it's stored in session and assign it to view so it can display
     * the login name
     *
     * @param MvcEvent $event
     */
    public function onBootstrap( MvcEvent $event ) {
        // Below are session variables, can used accross application directolly in layouts
        $viewModel = $event->getApplication()->getMvcEvent()->getViewModel();
        //$session = new Container('LineSession');
        $session = new Container(SESSION_NAME);
        $viewModel->userId = $session->offsetGet("userId");
        $viewModel->userName = $session->offsetGet("userName");
        $viewModel->userLogin = $session->offsetGet("userLogin");
        $viewModel->userEmail = $session->offsetGet("userEmail");
        $viewModel->userIsAdmin = $session->offsetGet("isAdmin");        
        
        // Attach event to verify the user has been logged in
        // More detailed sample at http://programming-tips.in/zend-framework-2-login-with-zend-auth/
        $eventManager = $event->getApplication()->getEventManager();
        $eventManager->attach(MvcEvent::EVENT_DISPATCH, array( $this, 'beforeDispatch' ), 100);
    }
    /**
     * Is the user allowed to see this content ?
     * @param MvcEvent $event
     */
    public function beforeDispatch(MvcEvent $event){
        // Get session information
        $session = new Container(SESSION_NAME);
        $request = $event->getRequest();
        $response = $event->getResponse();
        $requestUri = $request->getRequestUri();
        if( !$this->_isPublicPage($requestUri) ){
            if( !$session->offsetExists('userEmail') ){
                // User needs to be logged in
                $url = $event->getRequest()->getBaseUrl().'/login';
                $response->setHeaders ( $response->getHeaders()->addHeaderLine( 'Location', $url ) );
                $response->setStatusCode(302);
                $response->sendHeaders();
            }
        }
    }
    /**
     * This method validates whether is a public section or not
     *
     * @param varchar $uri
     * @return boolean True when it's a public section
     */
    private function _isPublicPage($uri)
    {
        // Add dummy base path, otherwise it will cycle the application
        $uri = 'base'.trim($uri);
        if( $uri=='base/' ) return true;
        
        $publics = [ '/login', '/logout', '/reset-password', '/set-vcode', '/user/validate-code', '/user/save-pwd',
            '/', '/home', '/about'
        ];
        foreach ($publics as $public)
        {
            $position = strpos( trim($uri), $public);
            if ( $position  > 0 ){
                return true;
            }
        }
        return false;
    }
}
