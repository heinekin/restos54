<?php
class My_Restcentre extends Zend_Rest_Controller
{
    private $_list;
    protected $no_results;

    public function init()
    {
        $this->no_results = array('status' => 'NO_RESULTS');
        //$this->_helper->viewRenderer->setNoRender();

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

        // do some processing...
        // Send the JSON response:
        $centre = new Centre();
        $centre = $centre->fetchAll();
        $essai = $centre->toArray();

        $tab = array('success' => 'true', 'message' => 'Chargement des données', 'data' => $essai);

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
        $id = $this->_getParam('id', 0);

        // do some processing...
        // Send the JSON response:
        $this->_helper->json($this->no_results);
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
        $id = $data['id'];
        unset($data['id']);

        $centre = new Centre();
        $res = $centre->fetchAll();
        $order = count($res);
        $result = $centre->fetchRow("nom='".$data['nom']."'");

        if(!is_null($result))
        {
            $tab = array('success' => 'Erreur !', 'message' => 'Le centre existe déjà !', 'data' => array('test' => 'test'));
        }
        else
        {
            $centre->createRow($data);
            $id = $centre->insert($data);

            $feature = new Feature();
            
            $row = $feature->createRow();
            $row->code = 'CENTRES_CENTRE_'. $id;
            $row->title = $data['nom'];
            $row->module = 'centres';
            $row->controller = 'Defaut';
            $row->action = 'index';
            $row->order = $order;
            $row->feature_type_id = 2;
            $row->parent_id = 2;
            $id_feature = $row->save();

            $row = $feature->createRow();
            $row->code = 'CENTRES_CENTRE_'. $id.'_BDS' ;
            $row->title = 'Bons de sortie';
            $row->module = 'centres';
            $row->controller = 'Bds';
            $row->action = 'index';
            $row->order = 1;
            $row->feature_type_id = 2;
            $row->parent_id =$id_feature;
            $id_feature = $row->save();

            $tab = array('success' => 'Création réussie', 'message' => 'Le centre \''.$data['nom'].'\' à été créé', 'data' => array('test' => 'test'));

        }
       $this->_helper->json($tab);
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
        $centre = new Centre();
        if(isset($tab_centre['nom']))
        {
            $nom = $tab_centre['nom'];


            
            $result = $centre->fetchRow("nom='".$nom."'");
            if(!is_null($result))
            {
                $tab = array('success' => 'Erreur !', 'message' => 'Le centre existe déjà !', 'data' => array('test' => 'test'));
            }
            else
            {
                $where = $centre->getAdapter()->quoteInto('id = ?', $id);
                $result = $centre->update($tab_centre, $where);

                $feature = new Feature();
                $row = $feature->update(array('title'=>$nom), "code='CENTRES_CENTRE_".$id."' OR code ='CENTRES_CENTRE_".$id."_BDS'");
         
                $tab = array('success' => 'Mise à jour réussie', 'message' => 'Le centre \''.$nom.'\' à été mis à jour', 'data' => array('test' => 'test'));

            }
        }
        else
        {
                $where = $centre->getAdapter()->quoteInto('id = ?', $id);
                $result = $centre->update($tab_centre, $where);

                $tab = array('success' => 'Mise à jour réussie', 'message' => 'Le centre à été mis à jour', 'data' => array('test' => 'test'));

        }


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


        $feature = new Feature();
        $row = $feature->delete("code='CENTRES_CENTRE_".$id."' OR code ='CENTRES_CENTRE_".$id."_BDS'");


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