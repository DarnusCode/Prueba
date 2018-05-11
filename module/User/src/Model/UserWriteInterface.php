<?php
namespace User\Model;

use User\Model\User;

/**
 * Interface defined for CREATE, UPDATE AND DELETE operations in our database
 * 
 * @author Julio_MOLINERO
 *
 */
interface UserWriteInterface
{
    /**
     * Persist a new user in the system.
     *
     * @param User $user The user to insert; may or may not have an identifier.
     * @return User The inserted user, with identifier.
     */
    public function insertUser(User $user);
    
    /**
     * Update an existing user in the system.
     *
     * @param User $user The user to update; must have an identifier.
     * @return User The updated user.
     */
    public function updateUser(User $user);
    
    /**
     * Update last login date of an existing user in the system.
     *
     * @param User $user The user to update; must have an identifier.
     * @return User The updated user.
     */
    public function updateUserLastLogin(User $user);
    
    /**
     * Mark user as an admin.
     *
     * @param User $user The user to be set as an admin, must have an identifier.
     * @return bool
     */
    public function setAdmin(User $user);
    
    /**
     * Mark user as a regular user.
     *
     * @param User $user The user to be set as regular, must have an identifier.
     * @return bool
     */
    public function unsetAdmin(User $user);
    
    /**
     * Mark user as an active.
     *
     * @param User $user The user to be set as an active, must have an identifier.
     * @return bool
     */
    public function setActive(User $user);
    
    /**
     * Mark user as unactive so cannot log in.
     *
     * @param User $user The user to be disabled, must have an identifier.
     * @return bool
     */
    public function unsetActive(User $user);
    
}