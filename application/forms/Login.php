<?php

class Application_Form_Login extends Zend_Form
{

    public function init()
    {
        $e = array();
        
        $e['email'] = new Zend_Form_Element_Text('email');
        
        $e['password'] = new Zend_Form_Element_Password('password');
        
        $e['login_submit'] = new Zend_Form_Element_Submit('login_submit');
        
        // $this->setAttrib('style', 'display: none;');
        $this->addElements($e);
    }


}

