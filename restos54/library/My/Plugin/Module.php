<?php
class My_Plugin_Module extends Zend_Controller_Plugin_abstract
{
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        My_View::chooseView($this->getRequest());
    }
}