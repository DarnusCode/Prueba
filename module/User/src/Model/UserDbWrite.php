<?php
namespace User\Model;

use User\Model\User;

use Zend\Db\Adapter\AdapterInterface;
use RuntimeException;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Update;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Validator\Db\RecordExists;

/**
 * Be sure to use DB Adapter with permissions to write
 * 
 * @author Julio_MOLINERO
 *
 */
class UserDbWrite implements UserWriteInterface 
{
    /**
     * @var AdapterInterface
     */
    private $db;
    
    /**
     * Table name
     * 
     * @var string
     */
    protected $_name = 'users';
    /**
     * @param AdapterInterface $db
     */
    public function __construct(AdapterInterface $db)
    {
        $this->db = $db;
    }
    /**
     * {@inheritDoc}
     * @see \User\Model\UserWriteInterface::insertUser()
     */
    public function insertUser(User $user){        
        // Declare input validators on user name and email
        $userNameExists = new \Zend\Validator\Db\RecordExists( [ 'table' => $this->_name,
            'field' => 'user', 'adapter' => $this->db,
        ]);
        $emailExists = new \Zend\Validator\Db\RecordExists( [ 'table' => $this->_name,
            'field' => 'email', 'adapter' => $this->db,
        ]);
        $validateEmail = new \Zend\Validator\EmailAddress();
        if( !$validateEmail->isValid( $user->getEmail() ) ){
            throw  new RuntimeException( 'Correo inv&aacute;lido '.$user->getEmail() );
        }
        // Perform validation
        if( $userNameExists->isValid( $user->getUser() ) || $emailExists->isValid( $user->getEmail() ) ){
            throw new RuntimeException( 'Usuario o correo ya existe' );
        }        
        // Proceed with the insert
        $insert = new Insert( $this->_name );
        $insert->values([
            'user' => $user->getUser(),
            'user_name' => $user->getUserName(),
            'password' => md5($user->getPassword()),
            'phone' => $user->getPhone(),
            'email' => $user->getEmail(),
            'code' => $user->getCode(),
            'degree' => $user->getDegree(),
            'have_project' => $user->getHaveProject(),
            'project_description' => $user->getProjectDescription(),
            'participants_number' => $user->getParticipantsNumber(),
            'date_created' => time(),
            'last_login' => $user->getLastLogin(),
            'is_admin' => $user->getIsAdmin(),
            'active' => $user->getActive(),
            'validation_code' => $user->getValidationCode(),
            'validation_code_expires' => $user->getValidationCodeExpires(),            
        ]);
        /**
         * 
         * @var \Zend\Db\Sql\Sql $sql
         */
        $sql = new Sql($this->db);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $result = $statement->execute();
        
        if (! $result instanceof ResultInterface) {
            throw new RuntimeException(
                'Database error occurred during user insert operation'
                );
        }
        $id = $result->getGeneratedValue();
        return new User( $id, $user->getUser(), $user->getUserName(), $user->getPassword(), $user->getPhone(), $user->getEmail(), $user->getCode(), 
            $user->getDegree(), $user->getHaveProject(), $user->getProjectDescription(), $user->getParticipantsNumber(),
            $user->getIsAdmin(), $user->getActive(), $user->getValidationCode(), $user->getValidationCodeExpires());
        
    }

    /**
     * Update user, please notice some fields are not allowed to be updated
     *
     * For now, only these fields are allowed
     *
     * 1.- Password
     * 
     * 
     * {@inheritDoc}
     * @see \User\Model\UserWriteInterface::updateUser()
     */    
    public function updateUser(User $user)
    {
        if ( !$user->getId() ) {
            throw new RuntimeException('Cannot update user; missing identifier');
        }
        // For now, password and time zone are the only ones to be updated
        $counter = 0;
        $array = [];
        if( !empty($user->getPassword()) ){
            $array['password'] = md5($user->getPassword());
            $counter++;
        }        
        // Have anything to update ?
        if( $counter>0 ) {
            $update = new Update( $this->_name );
            $update->set( $array );
            $update->where(['id = ?' => $user->getId()]);
            
            $sql = new Sql($this->db);
            $statement = $sql->prepareStatementForSqlObject($update);
            $result = $statement->execute();
            
            if (! $result instanceof ResultInterface) {
                throw new RuntimeException(
                    'Database error occurred during set admin user operation'
                    );
            }
        }
        return $user;        
    }

    /**
     * {@inheritDoc}
     * @see \User\Model\UserWriteInterface::updateUserLastLogin()
     */
    public function updateUserLastLogin(User $user)
    {
        if ( !$user->getId() ) {
            throw new RuntimeException('Cannot activate user; missing identifier');
        }
        $update = new Update( $this->_name );
        $update->set( [ 'last_login' => time() ] );
        $update->where(['id = ?' => $user->getId()]);
        
        $sql = new Sql($this->db);
        $statement = $sql->prepareStatementForSqlObject($update);
        $result = $statement->execute();
        
        if (! $result instanceof ResultInterface) {
            throw new RuntimeException(
                'Database error occurred during set active user operation'
                );
        }
        return $user;        
    }
    /**
     * {@inheritDoc}
     * @see \User\Model\UserCommandInterface::setAdmin()
     */
    public function setAdmin(User $user)
    {
        if ( !$user->getId() ) {
            throw new RuntimeException('Cannot set admin user; missing identifier');
        }
        $update = new Update( $this->_name );
        $update->set( [ 'is_admin' => 1 ] );
        $update->where(['id = ?' => $user->getId()]);
        
        $sql = new Sql($this->db);
        $statement = $sql->prepareStatementForSqlObject($update);
        $result = $statement->execute();
        
        if (! $result instanceof ResultInterface) {
            throw new RuntimeException(
                'Database error occurred during set admin user operation'
                );
        }
        return $user;
    }    
    /**
     * {@inheritDoc}
     * @see \User\Model\UserCommandInterface::unsetAdmin()
     */
    public function unsetAdmin(User $user)
    {
        if ( !$user->getId() ) {
            throw new RuntimeException('Cannot unset admin user; missing identifier');
        }
        $update = new Update( $this->_name );
        $update->set( [ 'is_admin' => 0 ] );
        $update->where(['id = ?' => $user->getId()]);
        
        $sql = new Sql($this->db);
        $statement = $sql->prepareStatementForSqlObject($update);
        $result = $statement->execute();
        
        if (! $result instanceof ResultInterface) {
            throw new RuntimeException(
                'Database error occurred during unset admin operation'
                );
        }
        return $user;
    }
    
    /**
     * {@inheritDoc}
     * @see \User\Model\UserCommandInterface::setActive()
     */
    public function setActive(User $user)
    {
        if ( !$user->getId() ) {
            throw new RuntimeException('Cannot activate user; missing identifier');
        }
        $update = new Update( $this->_name );
        $update->set( [ 'active' => 1 ] );
        $update->where(['id = ?' => $user->getId()]);
        
        $sql = new Sql($this->db);
        $statement = $sql->prepareStatementForSqlObject($update);
        $result = $statement->execute();
        
        if (! $result instanceof ResultInterface) {
            throw new RuntimeException(
                'Database error occurred during set active user operation'
                );
        }
        return $user;
    }
    
    /**
     * {@inheritDoc}
     * @see \User\Model\UserCommandInterface::unsetActive()
     */
    public function unsetActive(User $user)
    {
        if ( !$user->getId() ) {
            throw new RuntimeException('Cannot deactivate user; missing identifier');
        }
        $update = new Update( $this->_name );
        $update->set( [ 'active' => 0 ] );
        $update->where(['id = ?' => $user->getId()]);
        
        $sql = new Sql($this->db);
        $statement = $sql->prepareStatementForSqlObject($update);
        $result = $statement->execute();
        
        if (! $result instanceof ResultInterface) {
            throw new RuntimeException(
                'Database error occurred during deactivate user operation'
                );
        }
        return $user;
    }
}