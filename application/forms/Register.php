<?php

class Application_Form_Register extends Application_Form_BaseForm
{

    public function init()
    {
        $e = array();
        
        $this->addElement('text', 'usr_fname');
        $this->addElement('text', 'usr_lname');
        $this->addElement('text', 'usr_email');
        $this->addElement('password', 'usr_password');
        $this->addElement('password', 'usr_password2');
        $this->addElement('submit', 'register');

        $this->getElement('usr_fname')->setLabel('First Name')->setDecorators(array('HtmlTag'));
        
        //$this->setAttrib('style', 'display: none;');
        $this->setAttrib('id', 'register-form');
    }


}