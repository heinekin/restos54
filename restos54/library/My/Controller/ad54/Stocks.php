<?php
class My_Controller_ad54_Stocks extends Zend_Controller_Action {
    
    public $id_campagne;

    public function init()
    {
        $session = (Zend_Session::namespaceGet('Campagne'));
        $campagne = $session['camp'];
        $this->id_campagne = $campagne->getSelected();
    }
    public function indexAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);

    }
    public function stocksAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);

    }
    public function livraisonsAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);

    }
    public function suiviAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);

    }
     public function loadproductAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json', true);
        $sql = "SELECT p.id, p.reference, p.nom
                FROM produit AS p";
        $result = (array)Zend_Registry::get('db')->fetchAll($sql);

        $tab = array('success' => 'true', 'message' => 'Chargement des données', 'data' => $result);

        $this->_helper->json($tab);
    }
    public function savelivraisonAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json', true);
        $id = $this->_getParam('id', 0);
        $nb_colis = $this->_getParam('nb_colis', 0);
        $date = $this->_getParam('date', 0);

        $data['id_campagne'] = $this->id_campagne;
        $data['id_produit'] = $id;
        $data['nb_colis'] = $nb_colis;
        $data['date'] = substr($date,6) . '-' . substr($date,3,2).'-'.substr($date,0,2);

        $livraison = new Livraison();
        $livraison->insert($data);
        $tab = array('success' => 'true', 'message' => 'Chargement des données', 'data' => 'ok');
        $this->_helper->json($tab);
    }

    public function loadlivraisonAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json', true);
        $sql = "SELECT p.reference, p.nom, DATE_FORMAT(l.date, '%d/%m/%Y') as date, l.nb_colis, l.id
                FROM produit AS p
                INNER JOIN livraison AS l
                    ON l.id_produit = p.id
                    AND l.id_campagne = ".$this->id_campagne."
                ORDER BY l.date DESC";

        $result = (array)Zend_Registry::get('db')->fetchAll($sql);
        //Zend_Registry::get('logger')->write($result);
        $tab = array('success' => 'true', 'message' => 'Chargement des données', 'data' => $result);

        $this->_helper->json($tab);
    }
    
    public function loadsuiviAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json', true);
        $sql = "SELECT DISTINCT DATE_FORMAT(date, '%m/%Y') as mois from livraison where id_campagne=".$this->id_campagne." ORDER BY date ASC";
        $mois = (array)Zend_Registry::get('db')->fetchAll($sql);

        $sql = "SELECT DISTINCT id_produit from livraison where id_campagne=".$this->id_campagne;
        $result = (array)Zend_Registry::get('db')->fetchAll($sql);
        $total = null;
        foreach($result as $id_produit)
        {
            $sql="SELECT id_produit, DATE_FORMAT(date, '%m/%Y') as date, SUM(nb_colis) as nb_colis
                  FROM livraison
                  WHERE id_campagne=".$this->id_campagne."
                  AND id_produit=".$id_produit['id_produit']."
                  GROUP BY DATE_FORMAT(date, '%m/%Y')
                  ORDER BY date";
            $res = (array)Zend_Registry::get('db')->fetchAll($sql);
            foreach($res as $livraison)
            {
                $total[$id_produit['id_produit']][$livraison['date']] = $livraison['nb_colis'];
            }
            
        }
        $sql = "SELECT p.id, p.reference, p.nom, nc.nb_colis
                FROM produit AS p
                 LEFT OUTER JOIN nb_colis AS nc
                    ON p.id = nc.id_produit
                    AND
                        nc.id_campagne = ".$this->id_campagne;
        $result = (array)Zend_Registry::get('db')->fetchAll($sql);

        foreach($result as $k=>$produit)
        {
            $i=0;
            foreach($mois as $key=>$value)
            {
                $i++;
                if(array_key_exists($produit['id'], $total) && array_key_exists($value['mois'], $total[$produit['id']]))
                {

                    $result[$k]['livraison'.$i] = $total[$produit['id']][$value['mois']];
                }
                else
                {
                    $result[$k]['livraison'.$i] = 0;
                }
            }
        }
//Zend_Registry::get('logger')->write($result);
        $tab = array('success' => 'true', 'message' => 'Chargement des données', 'data' => $result);

        $this->_helper->json($tab);
    }

    public function loadpanelAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json', true);

        $id = $this->_getParam('id', 0);
        $livraison = $this->_getParam('class', 0);

         $sql = "SELECT DISTINCT DATE_FORMAT(date, '%m/%Y') as mois from livraison where id_campagne=".$this->id_campagne." ORDER BY date ASC";
        $mois = (array)Zend_Registry::get('db')->fetchAll($sql);

        $result = (array)Zend_Registry::get('db')->fetchAll($sql);
        
        $date=0;
        foreach($result as $k=> $month)
        {
            if(($k+1) == (int)substr($livraison, 9))
                $date = $month['mois'];
        }


        $sql = "SELECT p.reference, p.nom, DATE_FORMAT(l.date, '%d/%m/%Y') as mois, l.nb_colis, l.id
                FROM produit AS p
                INNER JOIN livraison AS l
                    ON l.id_produit = p.id
                    AND l.id_campagne = ".$this->id_campagne."
                    AND DATE_FORMAT(l.date, '%m/%Y') = '".$date."' 
                WHERE p.id = ".$id."
                ORDER BY l.date DESC";

        $result = (array)Zend_Registry::get('db')->fetchAll($sql);
        $tab = array('success' => 'true', 'message' => 'Chargement des données', 'data' => $result);

        $this->_helper->json($tab);
    }
}