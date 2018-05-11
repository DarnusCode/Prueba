<?php
namespace User\Model;

interface UserListInterface
{
    /**
     * Return a set of all users that we can iterate over.
     *
     * Each entry should be a User instance.
     *
     * @param boolean $paginated do we need pagination
     * @param string $textToSearch Search by username or email
     * @return User[]
     */
    public function findAllUsers( $paginated = false, $textToSearch = "" );
    
    /**
     * Return a single user.
     *
     * @param  int $id Identifier of the user to return.
     * @return User
     */
    public function findUser( $id );
    
    /**
     * Return a single user
     * 
     * @param string $user User login name
     * @return User
     */
    public function findUserLogin( $login );
    
    /**
     * Return a single user
     *
     * @param string $email User email
     * @return User
     */
    public function findUserEmail( $email );
    
}