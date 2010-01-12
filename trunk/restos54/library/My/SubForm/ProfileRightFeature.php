<?php
class My_SubForm_ProfileRightFeature  extends My_Form
{
    public $cbDecorators = array(
        'ViewHelper',
        'errors', 
        array('Fieldset', array('legend' => 'Droit par profil', 'style'=>'padding: 5px;')),
        array(array('data' => 'HtmlTag'), array('tag' => 'span')),
        array('Label', array('tag' => 'span', 'placement' => 'append', 'class' => 'label')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')), 
        array(array('row' => 'HtmlTag'), array('tag' => 'td', 'colspan' => 2, 'class' => 'subform'))
    );
    
    public function __construct($options = null) 
    {
        parent::__construct($options);
        
        $all = new Profile();
        $listProfiles = array();
        foreach($all->fetchAll() as $obj) {
            $listProfiles[$obj->id] = $obj->code;
        }
        
        $this->setName('profileRightFeatureSubForm');
        
        $profileRight = new Zend_Form_Element_MultiCheckbox('profileRight');
        $profileRight->setMultiOptions($listProfiles);
        $profileRight->setDecorators($this->cbDecorators);
                
        $this->addElements(array($profileRight));
    }
    
    public function loadDefaultDecorators()
    {
        $this->setDecorators(array('FormElements'));
    }
}