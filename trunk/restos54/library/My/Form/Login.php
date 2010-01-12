<?php

class My_Form_Login extends My_Form
{
    const MIN_CARACTERS_FOR_CREDENTIALS = 6;
    
    public $loginDecorators = array(
        'ViewHelper',
        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'style' => 'margin-bottom:10px;')),
        array('Label', array('tag' => 'div'))
    );

    public $pwdDecorators = array(
        'ViewHelper',
        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'style' => 'margin-bottom:5px;')),
        array('Label', array('tag' => 'div'))
    );
    
    public $submitDecorators = array(
        'ViewHelper',
        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'style' => 'position: relative; bottom:-5px;'))
    );

    public $noTagDecorators = array('ViewHelper');
    
    public function __construct($options = null)
    {
        parent::__construct($options);
        $this->setAction('/login/connecter');
        $this->setName('secureForm');

        $stringLength = new Zend_Validate_StringLength();
        $stringLength->setMin(self::MIN_CARACTERS_FOR_CREDENTIALS);
        
        $login = new Zend_Form_Element_Text('login');
        $login->setLabel('Utilisateur :');
        $login->setAttrib('size', '35');
        $login->setRequired(true)
        ->addFilter('StripTags')
        ->addFilter('StringTrim');
        $login->setDecorators($this->loginDecorators);
        $this->addElement($login);

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Mot de passe :');
        $password->setAttrib('size', '35');
        $password->setRequired(true)
        ->addFilter('StripTags')
        ->addFilter('StringTrim');
        $password->setDecorators($this->pwdDecorators);
        $this->addElement($password);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Connexion');
        $submit->setAttrib('id', 'submitbutton');
        $submit->setAttrib('class', 'auth_small_btn');
        $submit->setDecorators($this->noTagDecorators);
        $this->addElement($submit);

        $reset = new Zend_Form_Element_Reset('reset');
        $reset->setLabel('Réinitialiser');
        $reset->setAttrib('id', 'resetbutton');
        $reset->setAttrib('class', 'auth_small_btn');
        $reset->setDecorators($this->noTagDecorators);
        $this->addElement($reset);
    }
    
    public function loadDefaultDecorators()
    {
        $this->setDecorators(array(
                'FormElements',
                'Form'
        ));
    }
}
