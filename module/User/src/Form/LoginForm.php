<?php
namespace User\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

/**
 * This form is used to collect user's login, password and 'Remember Me' flag.
 */
class LoginForm extends Form
{
    /**
     * Constructor.     
     */
    public function __construct()
    {
        // Define form name
        parent::__construct('login-form');
     
        // Set POST method for this form
        $this->setAttribute('method', 'post');
                
        $this->addElements();
        $this->addInputFilter();          
    }
    
    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements() 
    {
        // Add "email" field
        $this->add([            
            'type'  => 'text',
            'name' => 'user',
            'options' => [
                'label' => 'Usuario',
            ],
        ]);
        
        // Add "password" field        
        $this->add([            
            'type'  => 'password',
            'name' => 'password',
            'options' => [
                'label' => 'Clave',
            ],
        ]);
        
        // Add "redirect_url" field
        $this->add([            
            'type'  => 'hidden',
            'name' => 'redirect_url'
        ]);
        
        // Add the CSRF field -> Cross-Site Request Forgery
        $this->add([
            'type' => 'csrf',
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                'timeout' => 600
                ]
            ],
        ]);
        
        // Add the Submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [                
                'value' => 'Sign in',
                'id' => 'submit',
            ],
        ]);
    }
    
    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter() 
    {
        // Create main input filter
        $inputFilter = new InputFilter();        
        $this->setInputFilter($inputFilter);
                
        // Add input for "user" field
        $inputFilter->add([
                'name'     => 'user',
                'required' => true,
                'filters'  => [
                    ['name' => 'StringTrim'],                    
                ],                
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 120
                        ],
                    ],
                    [
                        'name' => 'Regex',
                        'options' => [
                            // Username can be alphanum with dash, underscore, @ and periods and apostrophes
                            'pattern' => '/^([A-Za-z0-9-_@\.\']+)$/',
                            'messages' => [
                                \Zend\Validator\Regex::INVALID => 'Invalid login name'                                
                            ],                            
                        ],
                    ],
                ],
            ]);     
        
        // Add input for "password" field
        $inputFilter->add([
                'name'     => 'password',
                'required' => true,
                'filters'  => [                    
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 6,
                            'max' => 64
                        ],
                    ],
                ],
            ]);
        
        // Add input for "redirect_url" field
        $inputFilter->add([
                'name'     => 'redirect_url',
                'required' => false,
                'filters'  => [
                    ['name'=>'StringTrim']
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 0,
                            'max' => 2048
                        ]
                    ],
                ],
            ]);
    }        
}
