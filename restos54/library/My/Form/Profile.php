<?php

class My_Form_Profile extends My_Form
{
    public function __construct($withCode = true, $options = null) 
    {
        parent::__construct($options);
        
        $this->setName('profileForm');
        $this->setAttrib('onsubmit', 'submitFormAjax(this);return false;');
        
        $id = new Zend_Form_Element_Hidden('id');
        $id->setDecorators($this->hiddenDecorators);
        $this->addElement($id);
        
        if($withCode) {
            $code = new Zend_Form_Element_Text('code');
            $code->setLabel('Code');
            $code->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty', true)
                ->addValidator(new Zend_Validate_Alnum(true), true)
                ->addValidator(new Zend_Validate_StringLength(1, 255), true);
            $code->setDecorators($this->elementDecorators);
            $this->addElement($code);
        }
        
        $profileRight = new Zend_Form_Element_MultiCheckbox('profileRight');
        $profileRight->setMultiOptions($this->getListFeature());
        $profileRight->setDecorators($this->cbDecorators);
        $this->addElement($profileRight);
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton');
        $submit->setAttrib('class', 'small_btn');
        $submit->setDecorators($this->submitDecorators);
        $this->addElement($submit);
    }
    
    private function getListFeature()
    {
        $features = new Feature();
        $features->disableRelations(array('feature_type_id', 'parent_id'));
        $tree = $features->getTree();

        return $this->getFeatures($tree, null);
    }
    
    private function getFeatures($tree, $prefix = null)
    {
        if(is_null($prefix)) {
            $prefix = '';
        } else {
            $prefix .= '~~';
        }
                
        foreach($tree as $branche) {
            // on regarde si on a une traduction pour le titre du menu
            $menuTitle = $branche['title'];

            $list[$branche['id']] = $prefix . ' ' . $menuTitle;
            if(array_key_exists('children', $branche)) {
                $list += $this->getFeatures($branche['children'], $prefix);
            }
        }
        return $list;
    }
}