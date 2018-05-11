<?php
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\SessionManager;
use Zend\Session\Container;
use Zend\Uri\Uri;
use User\Form\LoginForm;
use Zend\Authentication\Result;
use Zend\View\Model\ViewModel;
use User\Model\UserListInterface;
use User\Model\UserWriteInterface;
use phpDocumentor\Reflection\Types\This;
use User\Model\User;


class AuthController extends AbstractActionController
{
    /**
     * @var \Zend\Authentication\AuthenticationService
     */
    protected $authService;
    // Properties
    /**
     * So we can perform READS
     *
     * @var UserInterface
     */
    private $userList;
    /**
     * So we can perform UPDATES, INSERTS AND DELETES
     *
     * @var UserCommandInterface
     */
    private $userWrite;    
    /**
     * 
     * @param \Zend\Authentication\AuthenticationService $authService
     * @param UserInterface $userList
     * @param UserCommandInterface $userCommand
     * @param UserLogInterface $userLog
     */
    public function __construct(\Zend\Authentication\AuthenticationService $authService, UserListInterface $userList, UserWriteInterface $userWrite)
    {
        // We should get the previously created AuthenticationService injected
        $this->authService = $authService;
        $this->userList = $userList;
        $this->userWrite = $userWrite;        
    }
    /**
     * Authenticates user given email address and password credentials.
     */
    public function loginAction()
    {        
        // If user has already signed in, redirect to home page
        if( $this->authService->hasIdentity() ){
            return $this->redirect()->toRoute('home');
        }        
        // Retrieve the redirect URL (if passed). We will redirect the user to this
        // URL after successfull login.
        $redirectUrl = (string)$this->params()->fromQuery('redirectUrl', '');
        if (strlen($redirectUrl)>2048) {
            throw new \Exception("Too long redirectUrl argument passed");
        }
        // Create login form
        $form = new LoginForm();
        $form->get('redirect_url')->setValue($redirectUrl);
    
        // Store login status.
        $isLoginError = false;
    
        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {
    
            // Fill in the form with POST data
            $data = $this->params()->fromPost();    
            $form->setData($data);
            // Validate form
            if( $form->isValid() ) {
    
                // Get filtered and validated data
                $data = $form->getData();
                $login = $data['user'];
    
                // Perform login attempt.
                /** @var \Zend\Authentication\Adapter\DbTable $adapter */
                $adapter = $this->authService->getAdapter();
                $adapter->setIdentity( $login );
                $adapter->setCredential( md5($data['password']) );
                $adapter->getDbSelect()->where( ['active = ?' => 1] ); // MUST be an active account
                //echo ($adapter->getDbSelect()->getSqlString());
                $result = $this->authService->authenticate();                
    
                // Check result.
                if ($result->isValid()) {
                    // Remember for 1 day
                    $session = new SessionManager();
                    $session->rememberMe(60*60*24*1);
                    // Update last login date and store information in session
                    $this->updateLastLogin($login);                    
                    // Get redirect URL.
                    $redirectUrl = $this->params()->fromPost('redirect_url', '');    
                    if (!empty($redirectUrl)) {
                        // The below check is to prevent possible redirect attack
                        // (if someone tries to redirect user to another domain).
                        $uri = new Uri($redirectUrl);
                        if (!$uri->isValid() || $uri->getHost()!=null)
                            throw new \Exception('Incorrect redirect URL: ' . $redirectUrl);
                    }    
                    // If redirect URL is provided, redirect the user to that URL;
                    // otherwise redirect to Home page.
                    if(empty($redirectUrl)) {
                        return $this->redirect()->toRoute('home');
                    } else {
                        $this->redirect()->toUrl($redirectUrl);
                    }
                } else {
                    $isLoginError = true;
                }
            } else {
                $isLoginError = true;                
            }
        }    
        return new ViewModel([
            'form' => $form,
            'isLoginError' => $isLoginError,
            'redirectUrl' => $redirectUrl
        ]);
    }
    /**
     * Clear user identity
     * @return \Zend\Http\Response
     */
    public function logoutAction()
    {        
        // Clear identity
        $this->authService->clearIdentity();
        // Remove session        
        $container = new Container(SESSION_NAME);
        $container->getManager()->destroy();
        $container->getManager()->getStorage()->clear(SESSION_NAME);
        // Redirect to login page
        return $this->redirect()->toRoute('home');
    }
    public function forbiddenAction() {
        // Dummy function, DO NOT remove !
    }
    /**
     * 
     * Update user last login date and also store information in session
     * @param string $login
     */
    private function updateLastLogin( $login, $returnToUser = 0 ){
        $user = $this->userList->findUserLogin($login);
        $this->userWrite->updateUserLastLogin($user);                
        // Store user's information in session
        $container = new Container(SESSION_NAME);
        $container->userId = $user->getId();
        $container->userName = $user->getUserName();
        $container->userLogin = $user->getUser();
        $container->userEmail = $user->getEmail();
        $container->isAdmin = $user->getIsAdmin();
    }    
    /**
     * Get Home URL
     * @return string
     */
    private function getPageURL() {
        // Chek if is secure server or not
        $pageURL = 'http';
        if (!empty($_SERVER['HTTPS'])) {$pageURL .= "s";}
        $pageURL .= "://";
        $pageURL .= $_SERVER["SERVER_NAME"];
         
        return $pageURL;
    }    
}