<?php
class My_Controller_centres_Defaut extends Zend_Controller_Action {

    public function indexAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);

    }
}