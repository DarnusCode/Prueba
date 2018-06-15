<?php
namespace Reservation\Model;

/**
 * Entity definition for table attendees
 *
 * CREATE TABLE `db_crm_line`.`attendees` (
 *   `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
 *   `event_id` INT UNSIGNED NOT NULL,
 *   `name` VARCHAR(120) NOT NULL,
 *   `email` VARCHAR(255) NOT NULL,
 *   PRIMARY KEY (`id`),
 *   UNIQUE INDEX `id_UNIQUE` (`id` ASC),
 *   INDEX `eventId_idx` (`event_id` ASC),
 *   CONSTRAINT `eventId`
 *   FOREIGN KEY (`event_id`)
 *   REFERENCES `db_crm_line`.`events` (`id`)
 *       ON DELETE NO ACTION
 *       ON UPDATE NO ACTION)
 *  ENGINE = InnoDB
 *  DEFAULT CHARACTER SET = utf8;   
 * 
 *
 */
class Attendee
{
    // Properties
    /**
     * 
     * @var number
     */
    private $id;
    /**
     * 
     * @var number
     */
    private $event_id;
    /**
     * 
     * @var string
     */
    private $name;
    /**
     * 
     * @var string
     */
    private $email;
    
    // Contructor
    public function __construct($id, $event_id, $name, $email){
        $this->id = (integer)$id;
        $this->event_id = (integer)$event_id;
        $this->name = substr($name, 0, 120); // to avoid data overloading
        $this->email = substr($email, 0, 255); // to avoid data overloading        
    }
    
    // Methods

    /**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param number $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return the $event_id
     */
    public function getEventId()
    {
        return $this->event_id;
    }

    /**
     * @param number $event_id
     */
    public function setEventId($event_id)
    {
        $this->event_id = $event_id;
    }

    /**
     * @return the $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return the $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }    
    
}