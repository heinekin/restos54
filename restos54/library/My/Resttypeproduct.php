<?php
class My_Resttypeproduct extends Zend_Rest_Controller
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
        $product = new ProductType();
        $product = $product->fetchAll();
        $essai = $product->toArray();

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
        unset($data['id']);

        $type = new ProductType();
        $result = $type->fetchRow("type='".$data['type']."'");
        if(!is_null($result))
        {
            $tab = array('success' => 'Erreur !', 'message' => 'Le type existe déjà !', 'data' => array('test' => 'test'));
        }
        else
        {
            $type->createRow($data);
            $id = $type->insert($data);

            $tab = array('success' => 'Création réussie', 'message' => 'Le type \''.$data['type'].'\' à été créé', 'data' => array('test' => 'test'));

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
        $tab_type = Zend_Json::decode($row[1]);
        $type = new ProductType();
        if(isset($tab_type['type']))
        {
            $nom = $tab_type['type'];



            $result = $type->fetchRow("type='".$nom."'");
            if(!is_null($result))
            {
                $tab = array('success' => 'Erreur !', 'message' => 'Le type existe déjà !', 'data' => array('test' => 'test'));
            }
            else
            {
                $where = $type->getAdapter()->quoteInto('id = ?', $id);
                $result = $type->update($tab_type, $where);

                $tab = array('success' => 'Mise à jour réussie', 'message' => 'Le type \''.$nom.'\' à été mis à jour', 'data' => array('test' => 'test'));

            }
        }
        else
        {
                $where = $type->getAdapter()->quoteInto('id = ?', $id);
                $result = $type->update($tab_type, $where);

                $tab = array('success' => 'Mise à jour réussie', 'message' => 'Le type à été mis à jour', 'data' => array('test' => 'test'));

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

        $type = new ProductType();
        $where = $type->getAdapter()->quoteInto('id = ?', $id);
        $result = $type->delete($where);

        $reponse = array('success' => 'Suppression réussie', 'message' => "Le type à été supprimé");
        $this->_helper->json($reponse);
    }
}