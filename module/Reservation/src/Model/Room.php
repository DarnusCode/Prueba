<?php
namespace Reservation\Model;

/**
 * Entity defintion for table rooms
 * 
 * CREATE TABLE `db_crm_line`.`rooms` (
 *   `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
 *   `name` VARCHAR(120) NULL,
 *   `description` VARCHAR(500) NULL,
 *   `capacity` INT(11) NOT NULL,
 *   `open_at` INT(11) NOT NULL,
 *   `close_at` INT(11) NOT NULL,
 *   PRIMARY KEY (`id`),
 *   UNIQUE INDEX `id_UNIQUE` (`id` ASC),
 *   UNIQUE INDEX `name_UNIQUE` (`name` ASC))
 *   ENGINE = InnoDB
 *   DEFAULT CHARACTER SET = utf8;
 * 
 *
 */
class Room
{
    // Properties
    /**
     * 
     * @var number
     */
    private $id;
    /**
     * 
     * @var string
     */
    private $name;
    /**
     * 
     * @var string
     */
    private $description;
    /**
     * 
     * @var number
     */
    private $capacity;
    /**
     * 
     * @var number, what time is available from (e.g. 8 means 8:00)
     */
    private $open_at;
    /**
     * 
     * @var number, what time closes (e.g. 20 means 20:00)
     */
    private $close_at;
    
    // Constructor
    public function __construct( $id, $name, $description, $capacity, $open_at, $close_at ){
        $this->id = (integer)$id;
        $this->name = substr($name, 0, 120); // To avoid data overloading
        $this->description = substr($description, 0, 500); // To avoid data overloading
        $this->capacity = (integer)$capacity;
        $this->open_at = (integer)$open_at;
        $this->close_at = (integer)$close_at;        
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
     * @return the $capacity
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * @param number $capacity
     */
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;
    }

    /**
     * @return the $open_at
     */
    public function getOpenAt()
    {
        return $this->open_at;
    }

    /**
     * @param \Model\number, $open_at
     */
    public function setOpenAt($open_at)
    {
        $this->open_at = $open_at;
    }

    /**
     * @return the $close_at
     */
    public function getCloseAt()
    {
        return $this->close_at;
    }

    /**
     * @param \Model\number, $close_at
     */
    public function setCloseAt($close_at)
    {
        $this->close_at = $close_at;
    }
    
}