<?php
class My_Controller_ad54_Campagne extends Zend_Controller_Action {

    public function indexAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);

    }
    public function createAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);

    }
    public function newAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json', true);

        $campagne = new Campagne();
        $result = $campagne->fetchRow("etat=1");
        if(!is_null($result))
        {
            $tab = array('failure' => 'true', 'error' => 'Vous devez d\'abord clôturer la campagne précédente.');
        }
        else
        {
            $campagne->insert(array( 'campagne' => $_POST['time'],
                                        'type' => $_POST['typeID'],
                                        'etat' => 1));
            $tab = array('success' => 'true');
        }

        
        $this->_helper->json($tab);
    }

    public function initproductAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);

    }

    public function closeAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);
    }
}