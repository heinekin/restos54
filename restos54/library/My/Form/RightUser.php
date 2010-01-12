<?php

class My_Form_RightUser extends My_Form
{
    private $_simple_decorator = array('ViewHelper');
    
    public function __construct($options = null) 
    {
        parent::__construct($options);
        
        $this->setName('rightUserForm');
        $this->setAttrib('onsubmit', 'submitFormAjax(this);return false;');
        
        $id = new Zend_Form_Element_Hidden('id');
        $id->setDecorators($this->hiddenDecorators);
        
        $this->addElement($id);
        $profile_id = new Zend_Form_Element_Hidden('profile_id');
        $profile_id->setDecorators($this->hiddenDecorators);
        $this->addElement($profile_id);
        
        foreach($this->getListFeature() as $idFeature => $title) {
            $right = new Zend_Form_Element_Radio('right_'.$idFeature);
            $right->setSeparator('&nbsp;');
            $right->setLabel($title);
            $right->setMultiOptions(array(1=>'autoriser', 0=>'interdire'));
            $right->setDecorators($this->radioDecorators);
            $this->addElement($right);
        }
        
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
            $prefix .= '--';
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