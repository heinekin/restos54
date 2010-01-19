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
    public function collecteAction()
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

        $stock_ad = new Stockad();

        $res = $stock_ad->fetchRow("id_campagne = ".  $this->id_campagne ." AND id_produit = ". $id);

                if(is_null($res))
                {
                    //insertion d une ligne
                    $data3['id_campagne'] = $this->id_campagne;
                    $data3['id_produit'] = $id;
                    $data3['stock'] = $nb_colis;
                    $data3['collecte'] = 0;
                    $stock_ad->insert($data3);
                }
                else
                {
                    $res = $res->toArray();
                    $data4['stock'] = $res['stock'] + $nb_colis;
                    $stock_ad->update($data4, "id_campagne = ".  $this->id_campagne ." AND id_produit = ". $id);
                    //update
                }

        $tab = array('success' => 'true', 'message' => 'Chargement des données', 'data' => 'ok');
        $this->_helper->json($tab);
    }
    public function savecollecteAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json', true);
        $id = $this->_getParam('id', 0);
        $nb_colis = $this->_getParam('nb_colis', 0);
        $date = $this->_getParam('date', 0);

        $data['id_campagne'] = $this->id_campagne;
        $data['id_produit'] = $id;
        $data['nb_colis'] = $nb_colis;
        $data['date'] = substr($date,6) . '-' . substr($date,3,2).'-'.substr($date,0,2);

        $collecte = new Collecte();
        $collecte->insert($data);

        $stock_ad = new Stockad();

        $res = $stock_ad->fetchRow("id_campagne = ".  $this->id_campagne ." AND id_produit = ". $id);

                if(is_null($res))
                {
                    //insertion d une ligne
                    $data3['id_campagne'] = $this->id_campagne;
                    $data3['id_produit'] = $id;
                    $data3['stock'] = 0;
                    $data3['collecte'] = $nb_colis;
                    $stock_ad->insert($data3);
                }
                else
                {
                    $res = $res->toArray();
                    $data4['collecte'] = $res['collecte'] + $nb_colis;
                    $stock_ad->update($data4, "id_campagne = ".  $this->id_campagne ." AND id_produit = ". $id);
                    //update
                }

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
                ORDER BY id DESC";

        $result = (array)Zend_Registry::get('db')->fetchAll($sql);
        $tab = array('success' => 'true', 'message' => 'Chargement des données', 'data' => $result);

        $this->_helper->json($tab);
    }

    public function loadcollecteAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json', true);
        $sql = "SELECT p.reference, p.nom, DATE_FORMAT(c.date, '%d/%m/%Y') as date, c.nb_colis,c.id
                FROM produit AS p
                INNER JOIN collecte AS c
                    ON c.id_produit = p.id
                    AND c.id_campagne = ".$this->id_campagne."
                ORDER BY id DESC";

        $result = (array)Zend_Registry::get('db')->fetchAll($sql);
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


    public function loadstockAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json', true);
        $sql = "SELECT p.id, p.reference, p.nom, p.portions, p.conditionnement, s.stock, s.collecte
                FROM produit AS p
                LEFT OUTER JOIN stock_ad AS s
                    ON p.id = s.id_produit
                    AND
                        s.id_campagne = ".$this->id_campagne;
        $result = (array)Zend_Registry::get('db')->fetchAll($sql);

        $tab = array('success' => 'true', 'message' => 'Chargement des données', 'data' => $result);

        $this->_helper->json($tab);
    }

    public function loadstatAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json', true);
        $id = $this->_getParam('id', 0);
        $stock = $this->_getParam('class', 0);
        if($stock == 'principal')
        {
            $sql = "SELECT p.reference, p.nom, DATE_FORMAT(l.date, '%d/%m/%Y') as mois, l.nb_colis, l.id
                    FROM produit AS p
                    INNER JOIN livraison AS l
                        ON l.id_produit = p.id
                        AND l.id_campagne = ".$this->id_campagne."
                    WHERE p.id = ".$id."
                    ORDER BY l.date DESC";
        }
        else
        {
            $sql = "SELECT p.reference, p.nom, DATE_FORMAT(c.date, '%d/%m/%Y') as mois, c.nb_colis, c.id
                    FROM produit AS p
                    INNER JOIN collecte AS c
                        ON c.id_produit = p.id
                        AND c.id_campagne = ".$this->id_campagne."
                    WHERE p.id = ".$id."
                    ORDER BY c.date DESC";
        }

        $result = (array)Zend_Registry::get('db')->fetchAll($sql);
foreach($result as $b => $a)
{
    if($stock == 'principal')
        $result[$b]['mouvement'] = "=> Livraison n° " . $a['id'];
    else
        $result[$b]['mouvement'] = "=> Collecte n° " . $a['id'];
}

if($stock == "principal")
{
        $sql = "SELECT p.reference, p.nom, DATE_FORMAT(bds.date, '%d/%m/%Y') as mois, bds.nb_colis, bds.id, c.nom as nom_centre
                FROM produit AS p
                INNER JOIN bon_de_sortie AS bds
                    ON bds.id_produit = p.id
                    INNER JOIN link_bds
                   ON link_bds.id_bds = bds.id_bds
                    AND link_bds.id_campagne = ".$this->id_campagne."
                    INNER JOIN centre as c
                        ON c.id = link_bds.id_centre
                WHERE p.id = ".$id."
                ORDER BY bds.date DESC";
}
else
{
            $sql = "SELECT p.reference, p.nom, DATE_FORMAT(bds.date, '%d/%m/%Y') as mois, bds.collecte as nb_colis, bds.id, c.nom as nom_centre
                FROM produit AS p
                INNER JOIN bon_de_sortie AS bds
                    ON bds.id_produit = p.id
                    INNER JOIN link_bds
                   ON link_bds.id_bds = bds.id_bds
                    AND link_bds.id_campagne = ".$this->id_campagne."
                    INNER JOIN centre as c
                        ON c.id = link_bds.id_centre
                WHERE p.id = ".$id."
                ORDER BY bds.date DESC";
}
        $result2 = (array)Zend_Registry::get('db')->fetchAll($sql);
        $nb = count($result);
        $i=0;
        foreach($result2 as $b => $a)
        {
            if($result2[$b]['nb_colis'] != 0)
            {
                $result2[$b]['mouvement'] = "<= Bon de sortie n°".$result2[$b]['id']."-Centre ".$result2[$b]['nom_centre'];
                unset ($result2[$b]['nom_centre']);
                $result[$nb + $i] = $result2[$b];
                $i++;
            }
        }

        $tab = array('success' => 'true', 'message' => 'Chargement des données', 'data' => $result);

       $this->_helper->json($tab);
    }

    public function loadofcAction()
    {
        //$this->getResponse()->setHeader('Content-Type', 'application/json', true);
$this->getHelper('viewRenderer')->setNoRender();
$params = $this->_getParam('id', 0);
$params = explode("_", $params);
$id = $params[0];
$stock = $params[1];

if($stock == 'principal')
        {
            $sql = "SELECT p.reference, p.nom, DATE_FORMAT(l.date, '%Y/%m/%d') as mois, l.nb_colis, l.id
                    FROM produit AS p
                    INNER JOIN livraison AS l
                        ON l.id_produit = p.id
                        AND l.id_campagne = ".$this->id_campagne."
                    WHERE p.id = ".$id."
                    ORDER BY l.date DESC";
        }
        else
        {
            $sql = "SELECT p.reference, p.nom, DATE_FORMAT(c.date, '%Y/%m/%d') as mois, c.nb_colis, c.id
                    FROM produit AS p
                    INNER JOIN collecte AS c
                        ON c.id_produit = p.id
                        AND c.id_campagne = ".$this->id_campagne."
                    WHERE p.id = ".$id."
                    ORDER BY c.date DESC";
        }

        $result = (array)Zend_Registry::get('db')->fetchAll($sql);
foreach($result as $b => $a)
{
    if($stock == 'principal')
        $result[$b]['mouvement'] = "=> Livraison";
    else
        $result[$b]['mouvement'] = "=> Collecte";
}

if($stock == "principal")
{
        $sql = "SELECT p.reference, p.nom, DATE_FORMAT(bds.date, '%Y/%m/%d') as mois, bds.nb_colis, bds.id, c.nom as nom_centre
                FROM produit AS p
                INNER JOIN bon_de_sortie AS bds
                    ON bds.id_produit = p.id
                    INNER JOIN link_bds
                   ON link_bds.id_bds = bds.id_bds
                    AND link_bds.id_campagne = ".$this->id_campagne."
                    INNER JOIN centre as c
                        ON c.id = link_bds.id_centre
                WHERE p.id = ".$id."
                ORDER BY bds.date DESC";
}
else
{
            $sql = "SELECT p.reference, p.nom, DATE_FORMAT(bds.date, '%Y/%m/%d') as mois, bds.collecte as nb_colis, bds.id, c.nom as nom_centre
                FROM produit AS p
                INNER JOIN bon_de_sortie AS bds
                    ON bds.id_produit = p.id
                    INNER JOIN link_bds
                   ON link_bds.id_bds = bds.id_bds
                    AND link_bds.id_campagne = ".$this->id_campagne."
                    INNER JOIN centre as c
                        ON c.id = link_bds.id_centre
                WHERE p.id = ".$id."
                ORDER BY bds.date DESC";
}
        $result2 = (array)Zend_Registry::get('db')->fetchAll($sql);
        $nb = count($result);
        $i=0;
        foreach($result2 as $b => $a)
        {
            if($result2[$b]['nb_colis'] != 0)
            {
                $result2[$b]['mouvement'] = "<= Bon de sortie n°".$result2[$b]['id'];
                unset ($result2[$b]['nom_centre']);
                $result[$nb + $i] = $result2[$b];
                $i++;
            }
        }
//Tri le tableau par date
 usort($result, array($this, "_sortDate"));
$data1 = array();
$data2 = array();
$cumul = 0;
$i=0;
$test = 0;
 foreach($result as $b => $a)
 {
     $date = $a['mois'];
     $date = substr($date,8) . '-' . substr($date,5,2).'-'.substr($date,0,4);
     $result[$b]['mois']= $date;

     if(strstr($a['mouvement'],"sortie"))
        $cumul -= $a['nb_colis'];
     else
        $cumul += $a['nb_colis'];     
        
     if(($test != $date) && ($test != 0))
     {
         $i++;
     }
     $data1[$i] = $cumul;
     $data2[$i] = $date;
     $test = $date;
 }

$max = max($data1);
$min = min($data1);
$y_max = round($max * 1.1,0);


if($min < 0)
    $y_min = round($min * 1.1,0);
else
    $y_min = round($min * 0.9,0);

$title = new title( 'Variation du stock de ce produit' );


$hol = new hollow_dot();
$hol->size(3)->halo_size(1)->tooltip('#x_label#<br>#val#');

$line = new line();
$line->set_default_dot_style($hol);
$line->set_values( $data1 );

$chart = new open_flash_chart();
$chart->set_title( $title );
$chart->add_element( $line );

$y = new y_axis();
$y->set_range( $y_min, $y_max, round(($y_max - $y_min)/10,0) );

$x = new x_axis();
$x->set_colour( '#428C3E' );
$x->set_grid_colour( '#86BF83' );

//
// Style the X Axis Labels:
//
$x_labels = new x_axis_labels();


// set them vertical
$x_labels->rotate(45);

// make them red/pink-ish
$x_labels->set_colour( '#CF4D5F' );

// set the label text
$x_labels->set_labels($data2);

//
// Add the X Axis Labels to the X Axis
//
$x->set_labels( $x_labels );

//
// Add the X Axis object to the chart:
//
$chart->set_x_axis( $x );
$chart->set_y_axis( $y );
$y_legend = new y_legend( 'Colis' );
$y_legend->set_style( '{font-size: 20px; color: #778877}' );
$chart->set_y_legend( $y_legend );

echo $chart->toPrettyString();

    }


    private function _sortDate($a, $b)
    {
        if($a['mois'] == $b['mois']) {
            return 0;
        }
        return ($a['mois'] < $b['mois']) ? -1 : 1;
    }


}