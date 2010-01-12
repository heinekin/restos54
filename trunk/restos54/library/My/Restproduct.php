<?php
class My_Restproduct extends Zend_Rest_Controller
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
        /*$product = new Product();
        $product = $product->fetchAll();
        $essai = $product->toArray();*/

        $sql = "SELECT p.id, p.reference, p.nom, p.conditionnement, p.poids, p.boitage, p.portions, g.gamme, t.type
                FROM produit AS p
                INNER JOIN product_type AS t
                    ON p.id_type = t.id
                INNER JOIN product_gamme AS g
                    ON p.id_gamme = g.id";
        $result = (array)Zend_Registry::get('db')->fetchAll($sql);
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
        $this->getResponse()->setHeader('Content-Type', 'application/json', true);
        $id = $this->_getParam('id', 0);
        $my = $this->_getAllParams();
        $json = $this->_request->getPost('data');
        $data = Zend_Json_Decoder::decode($json);
        
        unset($data['id']);

        $type = new ProductType();
        $result_type = $type->fetchRow("id=".$data['type']."");
        if(is_null($result_type))
        {
            $tab = array('success' => 'Erreur !', 'message' => 'Vous devez choisir le type du produit', 'data' => array('test' => 'test'));
        }
        else
        {
            $gamme = new ProductGamme();
            $result_gamme = $gamme->fetchRow("id=".$data['gamme']."");
            if(is_null($result_gamme))
            {
                $tab = array('success' => 'Erreur !', 'message' => 'Vous devez choisir la gamme du produit', 'data' => array('test' => 'test'));
            }
            else
            {
                $product = new Product();
                $data["id_gamme"] = $data["gamme"];
                $data["id_type"] = $data["type"];
                unset($data['gamme']);
                unset($data['type']);
                $product->insert($data);
                $tab = array('success' => 'Création réussie', 'message' => 'Le produit \''.$data['nom'].'\' à été créé', 'data' => array('test' => 'test'));

                
            }
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

        $tab_product = Zend_Json::decode($row[1]);

        $tab_product["id_gamme"] = $tab_product["gamme"];
        $tab_product["id_type"] = $tab_product["type"];
        unset($tab_product['gamme']);
        unset($tab_product['type']);
        unset($tab_product['id']);

        $type = new ProductType();
        $result = $type->fetchRow("type='".$tab_product['id_type']."'");
        if(!is_null($result))
        {
            $result = $result->toArray();
            $tab_product["id_type"] = $result['id'];
        }

        $gamme = new ProductGamme();
        $result = $gamme->fetchRow("gamme='".$tab_product['id_gamme']."'");
        if(!is_null($result))
        {
            $result = $result->toArray();
            $tab_product["id_gamme"] = $result['id'];
        }

        $product = new Product();


        $result = $product->fetchRow("reference=".$tab_product['reference']." AND nom='".$tab_product['nom']."' AND conditionnement='".$tab_product['conditionnement']."' AND poids='".$tab_product['poids']."' AND boitage='".$tab_product['boitage']."' AND portions=".$tab_product['portions']." AND id_type=".$tab_product['id_type']." AND id_gamme=".$tab_product['id_gamme']);
        if(!is_null($result))
        {
            $tab = array('success' => 'Erreur !', 'message' => 'Un produit à déjà toutes ces caractéristiques.', 'data' => array('test' => 'test'));
        }
        else
        {
            $where = $product->getAdapter()->quoteInto('id = ?', $id);
            $result = $product->update($tab_product, $where);
            $tab = array('success' => 'Mise à jour réussie', 'message' => 'Le produit \''.$id.'\' à été mis à jour', 'data' => array('test' => 'test'));
          
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
        $product = new Product();
        $where = $product->getAdapter()->quoteInto('id = ?', $id);
        $result = $product->delete($where);

        $reponse = array('success' => 'Suppression réussie', 'message' => "Le produit à été supprimé");
        $this->_helper->json($reponse);
    }
}