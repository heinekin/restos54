<?php

class My_Plugin_Campagne extends Zend_Controller_Plugin_abstract
{
    private $_camp = null;

    public function __construct()
    {
        $campSession = new Zend_Session_Namespace('Campagne');
        if(isset($campSession->camp)) {
            $this->_camp = $campSession->camp;
        }
    }

    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $auth = Zend_Auth::getInstance();

        if($auth->hasIdentity()) {

            $campagne = new Campagne();
            $result = $campagne->fetchRow("etat=1");
            if(!is_null($result))
            {
                $result = $result->toArray();
                $semaine = new Semaine();
                $sem = $semaine->fetchRow("id_campagne = ".$result['id']);
                $this->_camp = new My_Campagne();

                $this->_camp->setSelected($result['id']);
                $this->_camp->set_year($result['campagne']);
                $this->_camp->set_desc($result['description']);
                $this->_camp->set_type($result['type']);
                $this->_camp->setSemaine($sem['semaine']);
            }

            $campSession = new Zend_Session_Namespace('Campagne');
            $campSession->camp = $this->_camp;
        }
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $auth = Zend_Auth::getInstance();

        // si la personne est identifié
        if($auth->hasIdentity()) {

            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
            if($this->_camp instanceof My_Campagne){
                $viewRenderer->view->camp = "Campagne ".$this->_camp->get_type() . " - " . $this->_camp->get_year(). " - Semaine " .$this->_camp->getSemaine();

            }
            else
            {
                $viewRenderer->view->camp = "Aucune campagne n'est en cours, veuillez en créez une dans le menu campagne";
            }
        }
        
    }
}
 
