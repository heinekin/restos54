<?php
class My_Controller_ad54_Stocks extends Zend_Controller_Action {

    public function indexAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);

    }
public function stocksAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);

    }
}