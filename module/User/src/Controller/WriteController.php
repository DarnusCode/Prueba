<?php
namespace User\Controller;

use User\Model\User;
use Zend\Mvc\Controller\AbstractActionController;
use User\Model\UserListInterface;
use User\Model\UserWriteInterface;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class WriteController extends AbstractActionController
{
    // Properties
    /**
     * So we can perform READS
     * 
     * @var UserListInterface
     */
    private $userList;
    /**
     * So we can perform UPDATES, INSERTS AND DELETES
     * 
     * @var UserWriteInterface
     */
    private $userWrite;    
    /**
     * 
     * @var Container $_session
     */
    private $_session;
    
    // Constructor
    /**
     * Initialize interfaces
     * 
     * @param UserInterface $userList
     * @param UserCommandInterface $userCommand
     * @param UserLogInterface $userLog
     */
    public function __construct(UserListInterface $userList, UserWriteInterface $userWrite){
       $this->userList = $userList;
       $this->userWrite = $userWrite;       
       // Grab user's information in session
       $this->_session = new Container(SESSION_NAME);
       if( empty($_SESSION['csrf-token']) ){
           $_SESSION['csrf-token'] = base64_encode(openssl_random_pseudo_bytes(20));
       }
    }
    /**
     * Add a user to our database
     * @return \Zend\View\Model\ViewModel
     */
    public function registerAction(){
        // If user has already signed in, redirect to home page
        if( $this->_session->offsetGet('userId')!==null ){
            return $this->redirect()->toRoute('home');
        }
        $isError = false;        
        $user = new User( 0, "", "", "", "", "", "", "" ); // Define a dummy user
        $errorMessage = "";        
        // Check if user has submitted the form
        if ( $this->getRequest()->isPost() ) {
            // Get the data from post
            $data = $this->params()->fromPost();
            // var_dump( $data ); // Debug only
            $userName = $data['inputFirstname'];
            $loginName = $data['inputLoginName'];
            $phone = $data['inputPhoneNumber'];
            $password = $data['inputPassword'];
            $email = $data['inputEmail'];
            $passwordConfirmation = $data['inputPasswordConfirmation'];
            $code = $data['inputCode'];
            $degree = $data['inputDegree'];
            $haveProject = $data['inputProjectQuestion'];
            $projectDescription = $data['inputProjectDescription'];
            $participantsNumber = $data['inputParticipantsNumber'];
            $token = $data['inputCSRFToken'];
            $projectQuestion = $haveProject=='yes' ? 1 : 0;            
            // compare passwords            
            if( $password !== $passwordConfirmation ){
                $isError = true;
                $errorMessage = 'La contrase&ntilde;a y la confirmaci&oacute;n son diferentes';                    
            }
            // compare tokens to avoid CSRF
            if( $_SESSION['csrf-token'] != $token ){                                    
                $isError = true;
                $errorMessage = 'Token inv&aacute;lido';
            }
            if( !$isError ){                
                try {
                    $user = new User(0, $loginName, $userName, $password, $phone, $email, $code, $degree, $projectQuestion, $projectDescription, $participantsNumber );
                    $user = $this->userWrite->insertUser($user);
                    // Everything went well, redirect to confirmation message page
                    $this->redirect()->toRoute("registro-ok");
                } catch (\Exception $e) {
                    $isError = true;
                    $errorMessage = $e->getMessage();                    
                }                
            }
        }
        return new ViewModel([
            'isError' => $isError,
            'errorMessage' => $errorMessage,
            'user' => $user,
            'token' => $_SESSION['csrf-token'],            
        ]);
    }
    /**
     * Mark user as an admin
     *
     * @throws \InvalidArgumentException
     * @return \Zend\View\Model\JsonModel
     */
    public function setAdminAction() {
        $status = 'success';
        $message = '';
        try {
            $id = $this->params()->fromPost('id');
            if( !$id ){
                throw new \InvalidArgumentException('Missing user identifier');
            }
            // Get user first
            $user = $this->userList->findUser($id);
            // Admins only can do this
            if( $this->isAdmin() ){
                // Proceed to mark the user as a admin
                $user = $this->userWrite->setAdmin($user);                
            }            
            $message = 'User updated.';
        } catch (\Exception $e) {
            $status = 'error';
            $message = $e->getMessage();
        }
        // We will be using JSON in this case, no need to return a normal view
        return new JsonModel( [
            'status' => $status,
            'message' => $message,
        ] );
    }
    /**
     * Mark user as a regular
     *
     * @throws \InvalidArgumentException
     * @return \Zend\View\Model\JsonModel
     */
    public function unsetAdminAction() {
        $status = 'success';
        $message = '';
        try {
            $id = $this->params()->fromPost('id');
            if( !$id ){
                throw new \InvalidArgumentException('Missing user identifier');
            }
            // Get user first
            $user = $this->userList->findUser($id);
            // Admins only can do this
            if( $this->isAdmin() ){
                // Proceed to mark the user as a admin
                $user = $this->userWrite->unsetAdmin($user);                
            }            
            $message = 'User updated.';
        } catch (\Exception $e) {
            $status = 'error';
            $message = $e->getMessage();
        }
        // We will be using JSON in this case, no need to return a normal view
        return new JsonModel( [
            'status' => $status,
            'message' => $message,
        ] );
    }
    /**
     * Enable user
     *
     * @throws \InvalidArgumentException
     * @return \Zend\View\Model\JsonModel
     */
    public function setActiveAction() {
        $status = 'success';
        $message = '';
        try {
            $id = $this->params()->fromPost('id');
            if( !$id ){
                throw new \InvalidArgumentException('Missing user identifier');
            }
            // Get user first
            $user = $this->userList->findUser($id);
            // Admins only can do this
            if( $this->isAdmin() ){
                // Proceed to mark the user as a admin
                $user = $this->userWrite->setActive($user);
            }
            $message = 'User enabled.';
        } catch (\Exception $e) {
            $status = 'error';
            $message = $e->getMessage();
        }
        // We will be using JSON in this case, no need to return a normal view
        return new JsonModel( [
            'status' => $status,
            'message' => $message,
        ] );
    }
    /**
     * Disable user
     *
     * @throws \InvalidArgumentException
     * @return \Zend\View\Model\JsonModel
     */
    public function unsetActiveAction() {
        $status = 'success';
        $message = '';
        try {
            $id = $this->params()->fromPost('id');
            if( !$id ){
                throw new \InvalidArgumentException('Missing user identifier');
            }
            // Get user first
            $user = $this->userList->findUser($id);
            // Admins only can do this
            if( $this->isAdmin() ){
                // Proceed to mark the user as a admin
                $user = $this->userWrite->unsetActive($user);
            }
            $message = 'User disabled.';
        } catch (\Exception $e) {
            $status = 'error';
            $message = $e->getMessage();
        }
        // We will be using JSON in this case, no need to return a normal view
        return new JsonModel( [
            'status' => $status,
            'message' => $message,
        ] );
    }
    /**
     * 
     */
    public function registerConfirmationAction(){
        // If user has already signed in, redirect to home page
        if( $this->_session->offsetGet('userId')!==null ){
            return $this->redirect()->toRoute('home');
        }
        // Do not remove !, this shows the confirmation message and avoids page refreshing
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