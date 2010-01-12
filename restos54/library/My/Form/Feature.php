<?php

class My_Form_Feature  extends My_Form
{
    public function __construct($options = null) 
    {
        parent::__construct($options);
        
        $this->setName('featureForm');
        $this->setAttrib('onsubmit', 'submitFormAjax(this);return false;');
        
        $id = new Zend_Form_Element_Hidden('id');
        $id->setDecorators($this->hiddenDecorators);
        
        $code = new Zend_Form_Element_Text('code');
        $code->setLabel('Code');
        $code->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addFilter('StringToUpper')
            ->addValidator('NotEmpty', true)
            ->addValidator(new Zend_Validate_Regex('/^[A-Za-z0-9._-]+$/'), true)
            ->addValidator(new Zend_Validate_StringLength(1, 45), true);
        $code->setDecorators($this->elementDecorators);
        
        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Titre');
        $title->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true)
            ->addValidator(new Zend_Validate_Alnum(true), true)
            ->addValidator(new Zend_Validate_StringLength(1, 255), true);
        $title->setDecorators($this->elementDecorators);
        
        $module = new Zend_Form_Element_Text('module');
        $module->setLabel('Module');
        $module->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true)
            ->addValidator(new Zend_Validate_Regex('/^[A-Za-z0-9._-]+$/'), true)
            ->addValidator(new Zend_Validate_StringLength(1, 25), true);
        $module->setDecorators($this->elementDecorators);
        
        $controller = new Zend_Form_Element_Text('controller');
        $controller->setLabel('Controller');
        $controller->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true)
            ->addValidator('Alpha', true)
            ->addValidator(new Zend_Validate_StringLength(1, 25), true);
        $controller->setDecorators($this->elementDecorators);
        
        $action = new Zend_Form_Element_Text('page');
        $action->setLabel('Action');
        $action->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true)
            //->addValidator('Alpha', true)
            ->addValidator(new Zend_Validate_StringLength(1, 25), true);
        $action->setDecorators($this->elementDecorators);
        
        $order = new Zend_Form_Element_Text('order');
        $order->setLabel('Ordre');
        $order->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true)
            ->addValidator('Digits', true)
            ->addValidator(new Zend_Validate_GreaterThan('0'), true);
        $order->setDecorators($this->elementDecorators);
        
        $featureType = $this->getFeatureTypeElement();
        $featureType->setRequired(true)
            ->addValidator('NotEmpty', true)
            ->addValidator(new Zend_Validate_GreaterThan('0'), true);
        $featureType->setDecorators($this->elementDecorators);
        
        $featureParent = $this->getFeatureParentElement();
        $featureParent->setDecorators($this->elementDecorators);
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton');
        $submit->setAttrib('class', 'small_btn');
        $submit->setDecorators($this->submitDecorators);
        
        $profileRight = new My_SubForm_ProfileRightFeature();
        
        
        $this->addElements(array($id, $code, $title, $module, $controller, $action, $order, $featureType, $featureParent));
        $this->addSubForm($profileRight, 'subform');
        $this->addElements(array($profileRight, $submit));
        
    }
    
    private function getFeatureTypeElement() 
    {
        $all = new FeatureType();
        $listFeatureTypes = array();
        foreach($all->fetchAll() as $obj) {
            $listFeatureTypes[] = $obj;
        }
        
        $ft[0] = '';
        foreach($listFeatureTypes as $featureType) {
            try {
                $ft[$featureType->id] = $featureType->feature_type;
            } catch(Exception $e) {}
        }
        
        $el = new Zend_Form_Element_Select('featureType');
        $el->setMultiOptions($ft);
        $el->setLabel('Type de fonction');
        
        return $el;
    }
    
    private function getFeatureParentElement()
    {
        
        $all = new Feature();
        $all->disableRelations(array('feature_type_id', 'parent_id'));
        $listFeatures = $all->getTree(null, array('feature_type_id < 5'));
        
        $prefix = '';
        $f[0] = '';
        foreach($listFeatures as $feature) {
            try {
                $f[$feature['id']] = $feature['title'];
                if(array_key_exists('children', $feature)) {
                    $f = $this->getFeatureChildrenList($feature['children'], $f, $prefix);
                }
            } catch(Exception $e) {}
        }
        
        $el = new Zend_Form_Element_Select('featureParent');
        $el->setMultiOptions($f);
        $el->setLabel('Fonction parente');
        
        return $el;
    }
    
    private function getFeatureChildrenList($features = array(), $f = array(), $prefix = '')
    {
        $prefix .= '- - ';
        foreach($features as $feature) {
            try {
                $f[$feature['id']] = $prefix.$feature['title'];
                if(array_key_exists('children', $feature)) {
                    $f = $this->getFeatureChildrenList($feature['children'], $f, $prefix);
                }
            } catch(Exception $e) {}
        }
        return $f;
    }
}