<?php
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
// Consume User Interface
use User\Model\UserListInterface;
// Enable view model
use Zend\View\Model\ViewModel;
use User\Model\User;
//use Zend\Authentication\AuthenticationService;
use Zend\Session\Container;
// Needed to attach on bootstrap events
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;

class ListController extends AbstractActionController
{
    /**
     *
     * @var $userList
     */
    private $userList;
    private $_session;    
    public function __construct(UserListInterface $userList){
        $this->userList = $userList;
        // Store user's information in session
        $this->_session = new Container(SESSION_NAME);
    }    
    // Methods
    // Define what layout/template to use
    //
    // Example taken from
    // http://www.masterzendframework.com/views/change-layout-controllers-actions-zend-framework-2/
    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);
        // Attach event to verify the user has been logged in
        // TODO Verify the user has Admin permissions/settings
        // More detailed sample at http://programming-tips.in/zend-framework-2-login-with-zend-auth/
        $events->attach(MvcEvent::EVENT_DISPATCH, array( $this, 'beforeDispatch' ), 100);
    }
    /**
     * Is the user allowed to see this content ?
     * Only users flagged is_admin='1'
     * Logout action is open to all users so they can clear their sessions
     * @param MvcEvent $event
     */
    public function beforeDispatch(MvcEvent $event){
        $action = $event->getRouteMatch()->getParam('action');
        $protectedAction = [ 'index', 'user' ];
        // ====================================================
        // Only admins can see User List
        // ====================================================
        // Check is moderator and action name is not logout
        $isAdmin = $this->_session->offsetExists('isAdmin') ? $this->_session->offsetGet('isAdmin') : '0';
    
        if( (int)$isAdmin===0 && in_array( $action, $protectedAction ) ) {
            //throw new \Exception("You are not allowed to see this content");
            $response = $event->getResponse();            
            // User needs to be logged in
            $url = $event->getRequest()->getBaseUrl().'/forbidden';
            $response->setHeaders ( $response->getHeaders()->addHeaderLine( 'Location', $url ) );
            $response->setStatusCode(302);
            $response->sendHeaders();
        }
    }
    public function indexAction(){
        // Admin defined function only
        $this->checkAdminPermissions();
        // Define layout for this
        $this->layout()->setTemplate('layout/admin');
        // Grab the paginator from User Interface
        $textToSearch = $this->params()->fromQuery('textToSearch', null);
        $paginator = $this->userList->findAllUsers(true, $textToSearch);
        
        // Set the current page to what has been passed in query string,
        // or to 1 if none is set, or the page is invalid:
        $page = (int) $this->params()->fromQuery('page', 1);
        $page = ($page < 1) ? 1 : $page;
        $paginator->setCurrentPageNumber($page);
        
        // Set the number of items per page to 10:
        $paginator->setItemCountPerPage(10);
        
        return new ViewModel([
            'paginator' => $paginator,
            'currentUser' => $this->_session->offsetGet('userId'),
            'textToSearch' => $textToSearch,
        ]);
    }
    /**
     * Verifies the user has permissions to see some content
     */
    private function checkAdminPermissions(){
        if( !$this->isAdmin() ){
            $this->redirect()->toRoute('forbidden');
        }
    }
    /**
     * Validate current user is able to perform creations
     * @return bool
     */
    private function isAdmin():bool {
        // Get user id from session
        $userId = $this->_session->offsetGet('userId');
        // Look up for his settings
        $user = $this->userList->findUser( $userId );
        if( (integer)$user->getIsAdmin()===1 ){
            return true;
        } else {
            return false;
        }
    }
    
}