<?php
namespace Reservation\Model;

/**
 * Entity definition for table events
 * 
 * CREATE TABLE `db_crm_line`.`events` (
 *   `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
 *   `room_id` INT UNSIGNED NOT NULL,
 *   `title` VARCHAR(500) NOT NULL,
 *   `description` VARCHAR(1024) NULL,
 *   `start_at` INT(11) NOT NULL,
 *   `finish_at` INT(11) NOT NULL,
 *   `user_id` INT(11) NOT NULL;
 *   PRIMARY KEY (`id`),
 *   UNIQUE INDEX `id_UNIQUE` (`id` ASC),
 *   INDEX `roomsId_idx` (`room_id` ASC),
 *   CONSTRAINT `roomsId`
 *   FOREIGN KEY (`room_id`)
 *   REFERENCES `db_crm_line`.`rooms` (`id`)
 *       ON DELETE NO ACTION
 *       ON UPDATE NO ACTION)
 * ENGINE = InnoDB
 * DEFAULT CHARACTER SET = utf8;
 * 
 *
 */
class Event
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
    private $room_id;
    /**
     * 
     * @var string
     */
    private $title;
    /**
     * 
     * @var string
     */
    private $description;
    /**
     * 
     * @var number
     */
    private $start_at;
    /**
     * 
     * @var number
     */
    private $finish_at;
    /**
     * 
     * @var number
     */
    private $user_id;
    
    // Constructor
    public function __construct($id, $room_id, $title, $description, $start_at, $finish_at, $user_id){
        $this->id = (integer)$id;
        $this->room_id = (integer)$room_id;
        $this->title = substr($title, 0, 500); // to avoid data overloding
        $this->description = substr($description, 0, 1024); // to avoid data overloading
        $this->start_at = (integer)$start_at;
        $this->finish_at = (integer)$finish_at;
        $this->user_id = (integer)$user_id;        
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
     * @return the $room_id
     */
    public function getRoomId()
    {
        return $this->room_id;
    }

    /**
     * @param number $room_id
     */
    public function setRoomId($room_id)
    {
        $this->room_id = $room_id;
    }

    /**
     * @return the $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return the $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return the $start_at
     */
    public function getStartAt()
    {
        return $this->start_at;
    }

    /**
     * @param number $start_at
     */
    public function setStartAt($start_at)
    {
        $this->start_at = $start_at;
    }

    /**
     * @return the $finish_at
     */
    public function getFinishAt()
    {
        return $this->finish_at;
    }

    /**
     * @param number $finish_at
     */
    public function setFinishAt($finish_at)
    {
        $this->finish_at = $finish_at;
    }

    /**
     * @return the $user_id
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param number $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }
    
}