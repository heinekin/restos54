<?php
class My_Restqprev extends Zend_Rest_Controller
{
    private $_list;
    protected $no_results;

    public $id_campagne;

    public function init()
    {
        $session = (Zend_Session::namespaceGet('Campagne'));
        $campagne = $session['camp'];
        $this->id_campagne = $campagne->getSelected();
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

        $sql = "SELECT p.id, p.reference, p.nom, p.portions, p.conditionnement, i.inventaire, nc.nb_colis
                FROM produit AS p
                LEFT OUTER JOIN inventaire AS i
                    ON p.id = i.id_produit
                    AND
                        i.id_campagne = ".$this->id_campagne."
                LEFT OUTER JOIN nb_colis AS nc
                    ON p.id = nc.id_produit
                    AND
                        nc.id_campagne = ".$this->id_campagne;
        $result = (array)Zend_Registry::get('db')->fetchAll($sql);
        foreach($result as $b => $a)
        {
            if(is_null($a['nb_colis'])){
                $result[$b]['nb_colis'] = 0;}
            if(is_null($a['inventaire']))
                $result[$b]['inventaire'] = 0;
            
        }
        $tab = array('success' => 'true', 'message' => 'Chargement des données', 'data' => $result);

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
        $row = explode('data=', $tab[1]);
        $tab_colis = Zend_Json::decode($row[1]);
        $nb_colis = $tab_colis['nb_colis'];

        $colis = new Colis();

        $result = $colis->fetchRow("id_campagne='".$this->id_campagne."' AND id_produit='".$id."'");

        if(is_null($result))
        {
            $data = array(
                'id_campagne' => $this->id_campagne,
                'id_produit' => $id,
                'nb_colis' => $nb_colis
                         );
            $colis->insert($data);
        }
        else
        {
            $result = $result->toArray();
            
            $id_colis = $result['id'];
            
            $where = $colis->getAdapter()->quoteInto('id = ?', $id_colis);
            $result = $colis->update(array('nb_colis' => $nb_colis), $where);
        }
$tab = array('success' => 'Mise à jour réussie', 'message' => 'Le nb de colis du produit \''.$id.'\' à été mis à jour', 'data' => array('test' => 'test'));
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
        

    }
}