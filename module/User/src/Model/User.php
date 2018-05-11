<?php 
namespace User\Model;

/**
 * Entity definition for table users
 * 
 * CREATE TABLE `users` (
 *   `id` int(11) NOT NULL AUTO_INCREMENT,
 *   `user` varchar(120) NOT NULL,
 *   `user_name` varchar(120) NOT NULL,
 *   `password` varchar(20) NOT NULL,
 *   `phone` varchar(20) NOT NULL,
 *   `email` varchar(255) NOT NULL,
 *   `code` varchar(80) NOT NULL,
 *   `degree` varchar(255) NOT NULL,
 *   `have_project` int(1) NOT NULL DEFAULT '0',
 *   `project_description` varchar(2048) NULL,
 *   `participants_number` int(11) NOT NULL DEFAULT '0',
 *   `date_created` int(11) NOT NULL,
 *   `last_login` int(11) NOT NULL,
 *   `is_admin` int(1) NOT NULL DEFAULT '0',
 *   `active` int(1) NOT NULL DEFAULT '1',
 *   `validation_code` varchar(20) DEFAULT NULL,
 *   `validation_code_expires` int(11) DEFAULT NULL,
 *  PRIMARY KEY (`user`),
 *  UNIQUE KEY `id` (`id`),
 *  UNIQUE KEY `email` (`email`)
 *  ) ENGINE=InnoDB AUTO_INCREMENT=1491 DEFAULT CHARSET=utf8;
 *  
 * 
 * @author Julio Molinero
 *
 */
class User{
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
    private $user;
    /**
     *
     * @var string
     */
    private $user_name;
    /**
     * 
     * @var string
     */
    private $password;
    /**
     * 
     * @var string
     */
    private $phone;
    /**
     * 
     * @var string
     */
    private $email;
    /**
     * 
     * @var string
     */
    private $code;
    /**
     * 
     * @var string
     */
    private $degree;
    /**
     * 
     * @var number
     */
    private $have_project;
    /**
     * 
     * @var string
     */
    private $project_description;
    /**
     * 
     * @var number
     */
    private $participants_number;
    /**
     * 
     * @var number
     */
    private $date_created;
    /**
     * 
     * @var number
     */
    private $last_login;
    /**
     * 
     * @var number
     */
    private $is_admin;
    /**
     * 
     * @var number
     */
    private $active;
    /**
     * 
     * @var string
     */
    private $validation_code;
    /**
     * 
     * @var number
     */
    private $validation_code_expires;
    
    // Constructor
    public function __construct( $id, $user, $user_name, $password, $phone, $email, $code, $degree, $have_project = 0, $project_description = "", $participants_number = 0,
            $is_admin = 0, $active = 1, $validation_code = "", $validation_code_expires = 0, $last_login = 0, $date_created = 0){
        
        // Parse fields to avoid overloading, wrong data or data too long
        $this->id = (integer) $id;
        $this->user = substr($user,  0, 120);
        $this->user_name = substr($user_name,  0, 240);
        $this->password = substr($password, 0, 20);
        $this->phone = substr($phone, 0, 20);
        $this->email = substr($email, 0, 255);
        $this->code = substr($code, 0, 80);        
        $this->degree = substr($degree, 0, 255);
        $this->have_project = (integer)$have_project;
        $this->project_description = substr($project_description, 0, 2048);
        $this->participants_number = (integer)$participants_number;        
        $this->is_admin = (integer)$is_admin;
        $this->active = (integer)$active;
        $this->validation_code = substr($validation_code, 0, 20);
        $this->validation_code_expires = (integer)$validation_code_expires;
        $this->last_login = (integer)$last_login;
        $this->date_created = (integer)$date_created;
    }
    
    // Methods
    /**
     * 
     * @return number
     */
    public function getId(){
        return $this->id;
    }
    /**
     * 
     * @return string
     */
    public function getUser(){
        return $this->user;
    }
    /**
     *
     * @return string
     */
    public function getUserName(){
        return $this->user_name;
    }
    /**
     * 
     * @return string
     */
    public function getPassword(){
        return $this->password;
    }
    /**
     * 
     * @return string
     */
    public function getPhone(){
        return $this->phone;
    }
    /**
     * 
     * @return string
     */
    public function getEmail(){
        return $this->email;
    }
    /**
     * 
     * @return string
     */
    public function getCode(){
        return $this->code;
    }
    /**
     * 
     * @return string
     */
    public function getDegree(){
        return $this->degree;
    }
    /**
     * 
     * @return number
     */
    public function getHaveProject(){
        return $this->have_project;
    }
    /**
     * 
     * @return string
     */
    public function getProjectDescription(){
        return $this->project_description;
    }
    /**
     * 
     * @return number
     */
    public function getParticipantsNumber(){
        return $this->participants_number;
    }
    /**
     * 
     * @return number
     */
    public function getDateCreated(){
        return $this->date_created;
    }
    /**
     * 
     * @return number
     */
    public function getLastLogin(){
        return $this->last_login;
    }
    /**
     * 
     * @return number
     */
    public function getIsAdmin(){
        return $this->is_admin;
    }
    /**
     * 
     * @return number
     */
    public function getActive(){
        return $this->active;
    }
    /**
     * 
     * @return string
     */
    public function getValidationCode(){
        return substr(md5(rand()), rand(0, 20), 20);
    }
    /**
     * 
     * @return number
     */
    public function getValidationCodeExpires(){
        return $this->validation_code_expires;
    }    
}