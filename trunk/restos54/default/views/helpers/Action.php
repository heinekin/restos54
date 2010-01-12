<?php
class Zend_View_Helper_Action
{
    function action()
    {
        $fc = Zend_Controller_Front::getInstance();
        return $fc->getRequest()->getActionName();
    }
}