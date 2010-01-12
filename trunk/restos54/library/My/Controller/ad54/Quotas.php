<?php
class My_Controller_ad54_Quotas extends Zend_Controller_Action {
    
    public $id_campagne;
    public $lastWeek;
    public $prev;
    public $last_campagne;
    public $type;

    public function init()
    {
        $session = (Zend_Session::namespaceGet('Campagne'));
        $campagne = $session['camp'];
        $this->id_campagne = $campagne->getSelected();
        $this->type = $campagne->get_type();
        $this->lastWeek = $campagne->getSemaine() - 1;

        $sql = "SELECT id FROM campagne WHERE id < ".$this->id_campagne." AND type = '".$campagne->get_type()."' ORDER BY id DESC LIMIT 0,1";
        $result = (array)Zend_Registry::get('db')->fetchAll($sql);
         if(is_null($result))
         {
             $this->last_campagne = null;
         }
         else
         {
             $this->last_campagne = $result[0]['id'];
         }
        $cs = $this->_helper->getHelper('contextSwitch');
        $cs->addActionContext('loadofc', 'json');
        $cs->initContext();

    }
    public function indexAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);

    }
    public function quotasAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);
    }
    public function previsionsAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);
    }
    public function bnfAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);
    }
    public function loadquotasAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json', true);
        $id = $this->_getParam('index', 0);
        $type = $this->_getParam('type', 0);
        if($type == 'servis_old')
        {
            $sql = "SELECT c.nom as centre, p.repas_servis as repartition
                    FROM centre AS c
                    LEFT OUTER JOIN prevision AS p
                        ON  p.id_centre = c.id
                        AND
                            p.id_semaine = ".$id."
                        AND
                            p.id_campagne = ".$this->last_campagne;
        }
        elseif($type=='servis')
        {
            $sql = "SELECT c.nom as centre, p.repas_servis as repartition
                    FROM centre AS c
                    LEFT OUTER JOIN prevision AS p
                        ON  p.id_centre = c.id
                        AND
                            p.id_semaine = ".$id."
                        AND
                            p.id_campagne = ".$this->id_campagne;
        }
        elseif($type=='prevus')
        {
            $sql = "SELECT c.nom as centre, p.repas_prevus as repartition
                    FROM centre AS c
                    LEFT OUTER JOIN prevision AS p
                        ON  p.id_centre = c.id
                        AND
                            p.id_semaine = ".$id."
                        AND
                            p.id_campagne = ".$this->id_campagne;
        }
        else
            exit;
        

        $result = (array)Zend_Registry::get('db')->fetchAll($sql);

        $total = 0;
        foreach($result as $a)
        {
            $total += $a['repartition'];
        }

        foreach($result as $b => $a)
        {
            $result[$b]['repartition'] = (int)($result[$b]['repartition']);
            if($total == 0)
                $result[$b]['quotas'] = "0 %";
            else
                $result[$b]['quotas'] = round((($a['repartition'] / $total)*100), 2) . " %";
        }

        $reponse = array('success' => 'true', 'message' => "Chargement des quotas", 'data' => $result);
        $this->_helper->json($reponse);
    }
    public function loadtotalAction()
    {
$this->getResponse()->setHeader('Content-Type', 'application/json', true);

        $sql = "SELECT  p.repas_servis, p.repas_prevus, p.id_semaine as semaine
                FROM prevision as p
                WHERE
                        p.id_campagne = ".$this->id_campagne."
                ORDER BY semaine";

        $result = (array)Zend_Registry::get('db')->fetchAll($sql);
     //total repas servis par semaine
     $sem = null;
     $i=0;
     $data = array();
        foreach($result as $b => $a)
        {
            if(is_null($sem))
            {
                $data[$i]['repas_servis'] = (int)($result[$b]['repas_servis']);
                $data[$i]['repas_prevus'] = (int)($result[$b]['repas_prevus']);
                $data[$i]['semaine'] = (int)($result[$b]['semaine']);

            }
            else
            {
                if($sem != $a['semaine'])
                {
                    $i++;
                    $data[$i]['repas_servis'] = (int)($result[$b]['repas_servis']);
                    $data[$i]['repas_prevus'] = (int)($result[$b]['repas_prevus']);
                    $data[$i]['semaine'] = (int)($result[$b]['semaine']);
                }
                else
                {
                    $data[$i]['repas_servis'] += (int)($result[$b]['repas_servis']);
                    $data[$i]['repas_prevus'] += (int)($result[$b]['repas_prevus']);
                }
            }
            $sem = $a['semaine'];
        }
        $reponse = array('success' => 'true', 'message' => "Chargement des quotas", 'data' => $data);
       $this->_helper->json($reponse);
    }

    public function loadofcAction()
    {
        //$this->getResponse()->setHeader('Content-Type', 'application/json', true);
$this->getHelper('viewRenderer')->setNoRender();

        if(is_null($this->last_campagne))// si il ny a pas de campagne precedente, pas la peine de chercher les repas servis de la campagne precedente
        {
           $sql = "SELECT  p.repas_servis, p.repas_prevus, p.id_semaine as semaine, p.id_campagne
                FROM prevision as p
                WHERE
                        p.id_campagne = ".$this->id_campagne."
                ORDER BY semaine";
        }
        else
        {
            $sql = "SELECT  p.repas_servis, p.repas_prevus, p.id_semaine as semaine, p.id_campagne
                FROM prevision as p
                WHERE
                        p.id_campagne = ".$this->id_campagne."
                OR     p.id_campagne = ".$this->last_campagne."
                ORDER BY semaine";
        }

        $result = (array)Zend_Registry::get('db')->fetchAll($sql);
        
     //total repas servis par semaine
     $sem = null;
     $i=0;
     $data = array();
        foreach($result as $b => $a)
        {
            if(is_null($sem))
            {
                if(is_null($this->last_campagne))
                {
                    $data['repas_servis_old'][$i] = 0;
                    $data['repas_servis'][$i] = (int)($result[$b]['repas_servis']);
                }
                else
                {
                    if($result[$b]['id_campagne'] == $this->id_campagne)
                        $data['repas_servis'][$i] = (int)($result[$b]['repas_servis']);
                    else
                        $data['repas_servis_old'][$i] = (int)($result[$b]['repas_servis']);
                }
                $data['repas_prevus'][$i] = (int)($result[$b]['repas_prevus']);
                $data['semaine'][$i] = (int)($result[$b]['semaine']);

            }
            else
            {
                if($sem != $a['semaine'])
                {
                    if(!isset($data['repas_servis_old'][$i] ))$data['repas_servis_old'][$i] =0;
                    $i++;
                    if(is_null($this->last_campagne))
                    {
                        $data['repas_servis_old'][$i] = 0;
                        $data['repas_servis'][$i] = (int)($result[$b]['repas_servis']);
                    }
                    else
                    {
                        if($result[$b]['id_campagne'] == $this->id_campagne)
                            $data['repas_servis'][$i] = (int)($result[$b]['repas_servis']);
                        else
                            $data['repas_servis_old'][$i] = (int)($result[$b]['repas_servis']);
                    }
                    $data['repas_prevus'][$i] = (int)($result[$b]['repas_prevus']);
                    $data['semaine'][$i] = (int)($result[$b]['semaine']);
                }
                else
                {
                    if(is_null($this->last_campagne))
                    {
                        $data['repas_servis_old'][$i] = 0;
                        $data['repas_servis'][$i] += (int)($result[$b]['repas_servis']);
                    }
                    else
                    {
                        if($result[$b]['id_campagne'] == $this->id_campagne)
                        {
                            if(isset($data['repas_servis'][$i]))
                                $data['repas_servis'][$i] += (int)($result[$b]['repas_servis']);
                            else
                                $data['repas_servis'][$i] = (int)($result[$b]['repas_servis']);
                        }
                        else
                        {
                            if(isset($data['repas_servis_old'][$i]))
                                $data['repas_servis_old'][$i] += (int)($result[$b]['repas_servis']);
                            else
                                $data['repas_servis_old'][$i] = (int)($result[$b]['repas_servis']);
                        }
                    }
                    $data['repas_prevus'][$i] += (int)($result[$b]['repas_prevus']);
                }
            }
            $sem = $a['semaine'];
        }

foreach($data['semaine'] as $b => $semaine)
{
    $data['semaine'][$b] = "Semaine ".$semaine;
}
        $val = max($data['repas_servis'], $data['repas_prevus']);
        $val = max($val);
        $y_max = round($val * 1.1,0);


$bar = new bar_3d();
$bar->set_values( $data['repas_servis'] );
$bar->key('Repas servis', 12);
$bar->set_tooltip( "#val# repas servis" );
$bar->colour = '#D54C78';
$bar->set_on_click('barClicked');
$bar->set_on_click_text('servis');

$bar2 = new bar_3d();
$bar2->set_values( $data['repas_prevus'] );
$bar2->set_tooltip( "#val# repas prévus" );
$bar2->key('Repas prévus', 12);
$bar2->set_on_click('barClicked');
$bar2->set_on_click_text('prevus');

$bar3 = new bar_3d();
$bar3->set_values( $data['repas_servis_old'] );
$bar3->set_tooltip( "#val# repas servis l'année dernière" );
$bar3->colour( '#00FF00' );
$bar3->key('Repas servis campagne '.$this->type.' précedente', 12);
$bar3->set_on_click('barClicked');
$bar3->set_on_click_text('servis_old');

$x_labels = new x_axis_labels();
$x_labels->set_steps( 2 );
$x_labels->set_colour( '#A2ACBA' );
$x_labels->set_labels( $data['semaine'] );


$x_axis = new x_axis();
$x_axis->set_3d( 5 );
$x_axis->colour = '#909090';
$x_axis->set_labels( $x_labels );



$y = new y_axis();
$y->set_range( 0, $y_max, round($y_max/10,0) );

$chart = new open_flash_chart();
$chart->set_y_axis( $y );
$chart->set_x_axis( $x_axis );

$y_legend = new y_legend( 'Repas' );
$y_legend->set_style( '{font-size: 20px; color: #778877}' );
$chart->set_y_legend( $y_legend );
//
// here we add our data sets to the chart:
//
$chart->add_element( $bar );
$chart->add_element( $bar2 );
$chart->add_element( $bar3 );
echo $chart->toPrettyString();

    }
}