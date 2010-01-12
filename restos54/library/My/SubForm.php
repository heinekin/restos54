<?php

class My_SubForm extends Zend_Form_SubForm {

    public $elementDecorators = array(
        'ViewHelper',
        'errors', 
        array(array('data' => 'HtmlTag'), array('tag' => 'td')),
        array('Label', array('tag' => 'td')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
    );

    public $textareaDecorators = array(
        'ViewHelper',
        'errors',
        array(array('data' => 'HtmlTag'), array('tag' => 'td')),
        array('Label', array('tag' => 'td')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
    );

    public $hiddenDecorators = array(
        'ViewHelper',
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'hidden')),
        array(array('Label' => 'HtmlTag'), array('tag' => 'td', 'class' => 'hidden')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
    );

    public $submitDecorators = array(
        'ViewHelper',
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'align' => 'center', 'colspan' => 2)),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
    );

    public $radioDecorators = array(
        'ViewHelper',
        'errors',
        array(array('data' => 'HtmlTag'), array('tag' => 'td')),
        array('Label', array('tag' => 'td')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
    );

    public $cbDecorators = array(
        'ViewHelper',
        'errors',
        array(array('data' => 'HtmlTag'), array('tag' => 'span')),
        array('Label', array('tag' => 'span', 'placement' => 'append', 'class' => 'label')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        array(array('row' => 'HtmlTag'), array('tag' => 'td', 'colspan' => 2, 'class' => 'subform'))
    );

    public $datePickerDecorators = array(
        'ViewHelper',
        'errors', 
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'datePickerContainer')),
        array('Label', array('tag' => 'td')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
    );

    public $displayGroupDecorators = array(
        'FormElements',
        array('HtmlTag', array('tag' => 'table')),
        array('Fieldset', array('style'=>'padding: 5px; display: block;')),
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'colspan' => '2')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
    );

    public $displayHiddenGroupDecorators = array(
        'FormElements',
        array('HtmlTag', array('tag' => 'table')),
        array('Fieldset', array('style'=>'padding: 5px; display: none;')),
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'colspan' => '2')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
    );

    /**
    * construction du formulaire
    *
    * @param  mixed $options
    * @return void
    */
    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->setView(new Zend_View());

        // traduction des messages d'erreur de validation
        $langSession = new Zend_Session_Namespace('User_Lang');
        if($langSession->langs instanceof My_UserLangs) {
            $wordings = new My_Wording(null, $langSession->langs->getselected());
            $wordings->fetchPage('error_form');

            // a changer pour prendre en compte le nom de la langue
            $translate = new Zend_Translate('array', array_map('utf8_decode', $wordings->getWords()), 'fr');

            $this->setTranslator($translate);
        }
    }

    public function loadDefaultDecorators()
    {
        $this->setDecorators(array(
                'FormElements'/*,
                array('HtmlTag', array('tag' => 'table', 'class' => 'formTable')),
                'Fieldset',
                array(array('row' => 'HtmlTag'), array('tag' => 'td', 'colspan' => 2, 'class' => 'subform'))/*,
                array(array('row' => 'HtmlTag'), array('tag' => 'tr'))*/
        ));
    }
}

