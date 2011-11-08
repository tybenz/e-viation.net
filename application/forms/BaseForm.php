<?php

class Application_Form_BaseForm extends Zend_Form
{
    public function __construct($options = null) 
    { 
        //call parent contructor 
        parent::__construct($options);
        
        $this->setMethod('post');
        $this->addElementPrefixPath('Custom_Decorator', '/forms/decorators', 'decorator');
    } 
    
    public function setViewDecorators($options = array())
	{
        
        //$this->setDecorators(array('Form'));
        


    }
    
}