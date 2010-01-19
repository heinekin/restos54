<?php
class My_Controller_centres_Bds extends Zend_Controller_Action {

    public $id_centre;
    public $semaine;
    public $nom_centre;
    public $id_campagne;

    public function init()
    {
        $session = (Zend_Session::namespaceGet('Campagne'));
        $campagne = $session['camp'];
        $this->semaine = $campagne->getSemaine();
        $this->id_campagne = $campagne->getSelected();

    }

    public function indexAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);


    }

    public function bdsAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);
        $uri = $this->getRequest()->getRequestUri();
        $params = explode("/",$uri);
        $this->id_centre = $params[4];

        $session = new Zend_Session_Namespace('Centre');
        $session->centre = $params[4];

        $centre = new Centre();
        $result = $centre->fetchRow('id='.$this->id_centre);
        $this->nom_centre = $result['nom'];

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->view->centre = $this->nom_centre;
        $viewRenderer->view->semaine = $this->semaine;

        //$bds = new Bds();
       // $result = $bds->fetchRow("semaine=".$this->semaine." AND id_campagne = ".$this->id_campagne. ' AND id_centre = ' . $this->id_centre);
       // if(!$result)
            $viewRenderer->view->bds = 0;
      //  else
       //     $viewRenderer->view->bds = 1;

    }
    
    public function suiviAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);


    }
    
    public function loadbdsAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json', true);

        $sql = "SELECT p.id, p.reference, p.nom, p.conditionnement, p.poids, p.boitage, p.portions, g.gamme, t.type
                FROM produit AS p
                INNER JOIN product_type AS t
                    ON p.id_type = t.id
                INNER JOIN product_gamme AS g
                    ON p.id_gamme = g.id";
        $result = (array)Zend_Registry::get('db')->fetchAll($sql);
        foreach($result as $k=>$val)
        {
            $result[$k]['poids'] = (float)str_replace(',', '.', $val['poids']);
        }

        $tab = array('success' => 'true', 'message' => 'Chargement des données', 'data' => $result);

        $this->_helper->json($tab);
    }

    public function savebdsAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json', true);

        $session = (Zend_Session::namespaceGet('Centre'));
        $id_centre = $session['centre'];

        $date = $this->_request->getParam('date', 0);
        $ce = $this->_request->getParam('total_ce', 0);
        $grid = $this->_request->getParam('grid1', 0);
        $tab_bds = Zend_Json_Decoder::decode($grid);
        $bds = new Bds();
        $link_bds = new LinkBds();

        $sql = "SELECT b.*, lb.*
                FROM bon_de_sortie AS b
                INNER JOIN link_bds AS lb
                    ON b.id_bds = lb.id_bds";
        $result = (array)Zend_Registry::get('db')->fetchAll($sql);


        $datetime = date('Y-m-j H:i:s');
        $data2['id_campagne'] = $this->id_campagne;
        $data2['id_centre'] = $id_centre;
        $data2['id_bds'] = $datetime;
        $link_bds->insert($data2);
        $stock_ad = new Stockad();

        foreach($tab_bds as $k=> $val)
        {
            if(!empty($val['nb_colis']) || !empty($val['collecte']))
            {
                
                $data['id_bds'] = $datetime;
                $data['id_produit'] = $val['id'];
                $data['nb_colis'] = $val['nb_colis'];
                $data['collecte'] = $val['collecte'];
                $data['semaine'] = $this->semaine;
                $data['date'] = substr($date,6) . '-' . substr($date,3,2).'-'.substr($date,0,2);
                $bds->insert($data);

                $res = $stock_ad->fetchRow("id_campagne = ".  $this->id_campagne ." AND id_produit = ". $val['id']);

                if(is_null($res))
                {
                    //insertion d une ligne
                    $data3['id_campagne'] = $this->id_campagne;
                    $data3['id_produit'] = $val['id'];
                    $data3['stock'] = $val['nb_colis'];
                    $data3['collecte'] = $val['collecte'];
                    $stock_ad->insert($data3);
                }
                else
                {
                    $res = $res->toArray();
                    $data4['stock'] = $res['stock'] - $val['nb_colis'];
                    $data4['collecte'] = $res['collecte'] - $val['collecte'];
                    $stock_ad->update($data4, "id_campagne = ".  $this->id_campagne ." AND id_produit = ". $val['id']);
                  
                }

            }
        }
        $this->_helper->json(array('success' => 'true', 'message' => 'Chargement des données', 'data' => 'ok'));
    }
}