<?php
class My_Restbnf extends Zend_Rest_Controller
{
    protected $no_results;
    public $id_campagne;
    public $type_campagne;

    public function init()
    {
        $session = (Zend_Session::namespaceGet('Campagne'));
        $campagne = $session['camp'];
        $this->id_campagne = $campagne->getSelected();
        $this->type_campagne = $campagne->get_type();
    }

    /**
     * List
     *
     * The index action handles index/list requests; it responds with a
     * list of the requested resources.
     *
     * @return json
     */
    public function indexAction()
    {
    $this->getResponse()->setHeader('Content-Type', 'application/json', true);
    $id = $this->_getParam('id', 0);
    
        $tab = array('success' => 'true', 'message' => 'Chargement des données', 'data' => $id);

        $this->_helper->json($tab);
    }
    // 1.9.2 fix
    public function listAction() { return $this->_forward('index'); }

    /**
     * View
     *
     * The get action handles GET requests and receives an 'id' parameter; it
     * responds with the server resource state of the resource identified
     * by the 'id' value.
     *
     * @param integer $id
     * @return json
     */
    public function getAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json', true);
        $id_semaine = $this->_getParam('id', 0);

        // cherche la campagne précedente de même type
        $sql = "SELECT id FROM campagne WHERE id<".$this->id_campagne." AND type='".$this->type_campagne."'  ORDER BY id DESC LIMIT 0,1";
        $result = (array)Zend_Registry::get('db')->fetchAll($sql);
        $previous_id = $result[0]['id'];

        // cherche la semaine correspondante de la campagne précédente
        $semaine = new Semaine();
        $sem = $semaine->fetchRow("id_campagne = ".$previous_id);
        if(!is_null($sem))
            $nb_semaine_previous = $sem['semaine'];
        else
            $nb_semaine_previous = 1;

        $sql = "SELECT p.repas_servis, p.repas_prevus, p.id_semaine, p.id_centre
                    FROM prevision AS p
                    WHERE
                            p.id_campagne = ".$previous_id."
                    AND p.id_semaine=".$id_semaine;
            $result = (array)Zend_Registry::get('db')->fetchAll($sql);
        $centre = array();
        $total = 0;
        foreach($result as $a)
        {
            if(!array_key_exists($a['id_centre'], $centre)){
                $centre[$a['id_centre']] = $a['repas_servis'];
            }
            else
            {
                $centre[$a['id_centre']] += $a['repas_servis'];
            }
            $total += $a['repas_servis'];
        }



        $sql = "SELECT c.id, c.nom as centre, p.repas_servis, p.repas_prevus
                FROM centre AS c
                LEFT OUTER JOIN prevision AS p
                    ON  p.id_centre = c.id
                    AND
                        p.id_semaine = ".$id_semaine."
                    AND
                        p.id_campagne = ".$this->id_campagne;
        $result = (array)Zend_Registry::get('db')->fetchAll($sql);

        foreach($result as $b => $a)
        {
            if(array_key_exists($a['id'], $centre)){
                $moyenne =  $centre[$a['id']] / $nb_semaine_previous;
                $total_moyen = $total / $nb_semaine_previous;
                $result[$b]['parts'] = $moyenne;
                $result[$b]['repartition'] = round((($moyenne / $total_moyen)*100),2);
                
            }
            else
            {
                $result[$b]['parts'] = 0;
                $result[$b]['repartition'] = 0;
            }
            if(is_null($a['repas_servis'])){
                $result[$b]['repas_servis'] = 0;}
            if(is_null($a['repas_prevus']))
                $result[$b]['repas_prevus'] = 0;

        }
        $tab = array('success' => 'true', 'message' => 'Chargement des données', 'data' => $result);

        $this->_helper->json($tab);
             
    }

    /**
     * Create
     *
     * The post action handles POST requests; it accepts and digests a
     * POSTed resource representation and persists the resource state.
     *
     * @param integer $id
     * @return json
     */
    public function postAction()
    {
        $id = $this->_getParam('id', 0);
        $my = $this->_getAllParams();
        $json = $this->_request->getPost('data');
        $data = Zend_Json_Decoder::decode($json);
       
    }

    /**
     * Update
     *
     * The put action handles PUT requests and receives an 'id' parameter; it
     * updates the server resource state of the resource identified by
     * the 'id' value.
     *
     * @param integer $id
     * @return json
     */
    public function putAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json', true);
        $id = $this->_getParam('id', 0);
        $my = $this->_getAllParams();
        $data = $this->getRequest()->getRawBody();
        Zend_Json::decode($data);
        $data = urldecode($data);
        $tab = explode('&', $data);

        $id = explode('=', $tab[0]);
        $id = $id[1];
        $row = explode('=', $tab[1]);
        $tab_centre = Zend_Json::decode($row[1]);

        foreach($my as $k => $val)
        {
            if($k != 'module' && $k != 'action' && $k != 'controller'){
                $id_semaine = $k;
                $id_centre = $val;
            }
        }
        $sem = new Semaine();
        $res = $sem->fetchRow("id_campagne='".$this->id_campagne."' AND semaine >= ".$id_semaine);
        if(is_null($res))
        {
            //nouvelle semaine
            $sem->update(array('semaine'=>$id_semaine),"id_campagne=".$this->id_campagne);
        }
        $prev = new Prevision();
      $result = $prev->fetchRow("id_campagne='".$this->id_campagne."' AND id_centre='".$id_centre."' AND id_semaine='".$id_semaine."'");

      
        if(is_null($result))
        {
            $data = array(
                'id_campagne' => $this->id_campagne,
                'id_semaine' => $id_semaine,
                'id_centre' => $id_centre,
                'repas_prevus' => $tab_centre['repas_prevus'],
                'repas_servis' => $tab_centre['repas_servis']
                         );
            $prev->insert($data);
        }
        else
        {
            $result = $result->toArray();

            $id_centre = $result['id_prevision'];
            $where = $prev->getAdapter()->quoteInto('id_prevision = ?', $id_centre);
            $result = $prev->update(array('repas_prevus' => $tab_centre['repas_prevus'],'repas_servis' => $tab_centre['repas_servis']), $where);
        }
$tab = array('success' => 'Mise à jour réussie', 'message' => 'Les prévisions du centre\''.$tab_centre['centre'].'\' ont été mis à jour', 'data' => array('test' => 'test'));

        $this->_helper->json($tab);

    }

    /**
     * Delete
     *
     * The delete action handles DELETE requests and receives an 'id'
     * parameter; it updates the server resource state of the resource
     * identified by the 'id' value.
     *
     * @param integer $id
     * @return json
     */
    public function deleteAction()
    {
        $id = $this->_getParam('id', 0);
        $this->getResponse()->setHeader('Content-Type', 'application/json', true);
        $id = $this->_getParam('id', 0);
        $my = $this->_getAllParams();
        $data = $this->getRequest()->getRawBody();
        Zend_Json::decode($data);
        $data = urldecode($data);
        $data = explode("\"", $data);

        $id = $data[1];
        $centre = new Centre();
        $where = $centre->getAdapter()->quoteInto('id = ?', $id);
        $result = $centre->delete($where);

        $luc = new LinkUserCentre();
        $result = $luc->fetchAll("id_centre='".$id."'");
        $result = $result->toArray();
        if(empty($result))
        {
           $reponse = array('success' => 'Suppression réussie', 'message' => "Le centre à été supprimé");
        }
        else
        {
            $list = '<ul>';
            $u = new User();
            foreach($result as $user)
            {
                $where = $luc->getAdapter()->quoteInto('id = ?', $user['id']);
                $luc->update(array('id_centre' => 0),$where);

                $name = $u->fetchRow("id=".$user['id_user']);
                $name = $name->toArray();
                $list .= '<li> >'.$name['Nom'].' '.$name['Prenom'].'</li>';
            }
            $reponse =  array('success' => 'Suppression réussie', 'message' => "Le centre à été supprimé, mais les utiilisateurs suivant n'ont plus de centres : <br><ul>".$list."</ul>");
            
        }

       $this->_helper->json($reponse);
    }
}