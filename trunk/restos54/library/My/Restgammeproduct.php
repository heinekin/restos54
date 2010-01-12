<?php
class My_Restgammeproduct extends Zend_Rest_Controller
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
        $product = new ProductGamme();
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

        $gamme = new ProductGamme();
        $result = $gamme->fetchRow("gamme='".$data['gamme']."'");
        if(!is_null($result))
        {
            $tab = array('success' => 'Erreur !', 'message' => 'La gamme existe déjà !', 'data' => array('test' => 'test'));
        }
        else
        {
            $gamme->createRow($data);
            $id = $gamme->insert($data);

            $tab = array('success' => 'Création réussie', 'message' => 'La gamme \''.$data['gamme'].'\' à été créée', 'data' => array('test' => 'test'));

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
        $tab_gamme = Zend_Json::decode($row[1]);
        $gamme = new ProductGamme();
        if(isset($tab_gamme['gamme']))
        {
            $nom = $tab_gamme['gamme'];



            $result = $gamme->fetchRow("gamme='".$nom."'");
            if(!is_null($result))
            {
                $tab = array('success' => 'Erreur !', 'message' => 'La gamme existe déjà !', 'data' => array('test' => 'test'));
            }
            else
            {
                $where = $gamme->getAdapter()->quoteInto('id = ?', $id);
                $result = $gamme->update($tab_gamme, $where);

                $tab = array('success' => 'Mise à jour réussie', 'message' => 'La gamme \''.$nom.'\' à été mise à jour', 'data' => array('test' => 'test'));

            }
        }
        else
        {
                $where = $gamme->getAdapter()->quoteInto('id = ?', $id);
                $result = $gamme->update($tab_gamme, $where);

                $tab = array('success' => 'Mise à jour réussie', 'message' => 'La gamme à été mise à jour', 'data' => array('test' => 'test'));

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

        $gamme = new ProductGamme();
        $where = $gamme->getAdapter()->quoteInto('id = ?', $id);
        $result = $gamme->delete($where);

        $reponse = array('success' => 'Suppression réussie', 'message' => "La gamme à été supprimée");
        $this->_helper->json($reponse);
    }
}