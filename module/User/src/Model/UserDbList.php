<?php 
namespace User\Model;

use Zend\Hydrator\Reflection as ReflectionHydrator;
use Zend\Db\Adapter\AdapterInterface;

use InvalidArgumentException;
use RuntimeException;

use Zend\Hydrator\HydratorInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Sql;
// Needed for pagination
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\Predicate;

class UserDbList implements UserListInterface {
    
    // Properties
    protected $_name = 'users';
    
    /**
     * @var AdapterInterface
     */
    private $db;
    /**
     * @var HydratorInterface
     */
    private $hydrator;
    
    /**
     * @var User
     */
    private $userPrototype;
    
    // Constructor
    /**
     * @param AdapterInterface $db
     */
    public function __construct(AdapterInterface $db )
    {
        $this->db = $db;
        $this->hydrator      = new ReflectionHydrator();
        $this->userPrototype = new User( 0, "", "", "", "", "", "", "" );
    }
    /**
     * {@inheritDoc}
     * @see \User\Model\UserListInterface::findAllUsers()
     */
    public function findAllUsers($paginated = false, $textToSearch = "")
    {
        $sql    = new Sql($this->db);
        $select = $sql->select( $this->_name );
        // Apply filters if needed
        if( strlen($textToSearch) > 0 ){
            $select->where( ['user LIKE ? ' => "%$textToSearch%"] );
            $select->where( ['email LIKE ? ' => "%$textToSearch%"], Predicate::OP_OR );
            $select->where( ['user_name LIKE ? ' => "%$textToSearch%"], Predicate::OP_OR );
        }
        $stmt   = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        // Are we getting what is expected?
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            return [];
        }
        $resultSet = new HydratingResultSet( $this->hydrator, $this->userPrototype );
        // Are we using pagination ?
        if( $paginated ){
            $resultSet->setObjectPrototype( $this->userPrototype );
            // Create a new pagination adapter object:
            $paginatorAdapter = new DbSelect($select, $this->db, $resultSet);
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        } else {
            $resultSet->initialize($result);
            return $resultSet;
        }        
    }

    /**
     * {@inheritDoc}
     * @see \User\Model\UserListInterface::findUser()
     */
    public function findUser($id)
    {
        $sql       = new Sql($this->db);
        $select    = $sql->select( $this->_name );
        $select->where(['id = ?' => $id]);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();
        
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            throw new RuntimeException(sprintf(
                'Failed retrieving user with identifier "%s"; unknown database error.',
                $id
                ));
        }
        $resultSet = new HydratingResultSet($this->hydrator, $this->userPrototype);
        $resultSet->initialize($result);
        $user = $resultSet->current();
        if (! $user) {
            throw new InvalidArgumentException( sprintf('User with identifier "%s" not found.',$id) );
        }
        return $user;
    }

    /**
     * {@inheritDoc}
     * @see \User\Model\UserListInterface::findUserLogin()
     */
    public function findUserLogin($login)
    {
        $sql       = new Sql($this->db);
        $select    = $sql->select( $this->_name );
        $select->where(['user = ?' => $login]);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        // Get SQL String, DEBUG ONLY !!!
        //        echo $sql->buildSqlString( $select, $this->db ); die();
        $result    = $statement->execute();
        
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            throw new RuntimeException(sprintf(
                'Failed retrieving user with login "%s"; unknown database error.',
                $login
                ));
        }
        $resultSet = new HydratingResultSet($this->hydrator, $this->userPrototype);
        $resultSet->initialize($result);
        $user = $resultSet->current();
        if (! $user) {
            throw new InvalidArgumentException( sprintf('Usuario "%s" no existe.',$login) );
        }
        return $user;        
    }

    /**
     * {@inheritDoc}
     * @see \User\Model\UserListInterface::findUserEmail()
     */
    public function findUserEmail($email)
    {
        $sql       = new Sql($this->db);
        $select    = $sql->select( $this->_name );
        $select->where(['email = ?' => $email]);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        // Get SQL String, DEBUG ONLY !!!
        //        echo $sql->buildSqlString( $select, $this->db ); die();
        $result    = $statement->execute();
        
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            throw new RuntimeException(sprintf(
                'Failed retrieving user with email "%s"; unknown database error.',
                $email
                ));
        }
        $resultSet = new HydratingResultSet($this->hydrator, $this->userPrototype);
        $resultSet->initialize($result);
        $user = $resultSet->current();
        if (! $user) {
            throw new InvalidArgumentException( sprintf('Correo "%s" no existe.',$email) );
        }
        return $user;        
    }    
}