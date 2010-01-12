<?php
class My_Rest extends Zend_Rest_Controller
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
        $user = new User();
        $users = $user->fetchAll();
        $essai = $users->toArray();
$test = (array)$user->fetchWithCentre();

foreach($test as $key => $value)
{
    $test[$key]['centre'] = $test[$key]['nom'];
    unset($test[$key]['nom']);
}

        $tab = array('success' => 'true', 'message' => 'Chargement des données', 'data' => $test);

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
        $id_user = $data['id'];
        $id_centre = $data['centre'];
        $id_profile = $data['profile'];
        $data['profile_id'] = $id_profile;
        unset($data['id']);
        unset($data['centre']);
        unset($data['profile']);
        $user = new User();
        $result = $user->fetchRow("login='".$data['login']."' AND password='".$data['password']."'");
        if(!is_null($result))
        {
            $tab = array('success' => 'Erreur !', 'message' => 'Le couple login/password existe déjà pour un utilisateur, veuillez choisir un autre mot de passe', 'data' => array('test' => 'test'));
        }
        else
        {
            $user->createRow($data);
            $id = $user->insert($data);
           
            $tab = array('success' => 'Création réussie', 'message' => 'Le nouvel utilisateur à été créé', 'data' => array('test' => 'test'));

            $centre = new LinkUserCentre();

            $data2 = array('id_user' =>(int)$id, 'id_centre' => $id_centre) ;
            $centre->insert($data2);

            $userRight = new UserRightFeature();

            // on détermine les valeur qu'il faut mettre à jour
            // en faisant la différence avec les droits du profil
            $rightsP = new ProfileRightFeature();
            $rightsP->disableRelations(array('feature_id', 'profile_id'));
            $result = $rightsP->fetchAll('profile_id = '.$id_profile);
            foreach($result as $profile) {
                $row = $userRight->createRow();
                $row->user_id = $id;
                $row->feature_id = $profile->feature_id;
                $row->right = 1;
                $row->save();
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
        $tab_user = Zend_Json::decode($row[1]);
        $id = $tab_user['id'];

        if(isset($tab_user['centre']))
        {
            $centre = $tab_user['centre'];
            unset($tab_user['centre']);
        }
        if(isset($tab_user['profile']))
        {
            $tab_user['profile_id'] = $tab_user['profile'];
            unset($tab_user['profile']);
        }
        $user = new User();

        // Si l'utilisateur met a jour le mot de passe ou le login,
        // il faut verifier que ce nouveau couple n'existe pas deja pour un autre utilisateur
        if(isset($tab_user['login']) || isset($tab_user['password']))
        {
                if(!isset($tab_user['login']))
                {
                    $result = $user->fetchRow("id='".$id."'");
                    $result = $result->toArray();
                    $login = $result['login'];
                }
                else
                {
                    $login = $tab_user['login'];
                }
                if(!isset($tab_user['password']))
                {
                    $result = $user->fetchRow("id='".$id."'");
                    $result = $result->toArray();
                    $password = $result['password'];
                }
                else
                {
                    $password = $tab_user['password'];
                }
                $result = $user->fetchRow("login='".$login."' AND password='".$password."' AND NOT id='".$id."'");

                if(!is_null($result)) // Le couple existe deja
                {
                    $reponse = array('success' => 'Erreur !', 'message' => 'Le couple login/password existe déjà pour un utilisateur, veuillez choisir un autre mot de passe', 'data' => array('test' => 'test'));
                }
                else
                {
                    $where = $user->getAdapter()->quoteInto('id = ?', $id);
                    $result = $user->update($tab_user, $where);
                    $reponse = array('success' => 'Mise à jour réussie', 'message' => "Les données de l'utilisateur ont été modifiées", 'data' => array("ok" => "ok"));

                    if(isset($centre) && $centre != 0)
                    {
                        $luc = new LinkUserCentre();
                        $where = $user->getAdapter()->quoteInto('id_user = ?', $id);
                        $luc->update(array('id_centre' => $centre),$where);
                    }
                }
        }
        else
        {
            $where = $user->getAdapter()->quoteInto('id = ?', $id);
            $result = $user->update($tab_user, $where);

            if(isset($centre) && $centre != 0)
            {
                $luc = new LinkUserCentre();
                $where = $user->getAdapter()->quoteInto('id_user = ?', $id);
                $luc->update(array('id_centre' => $centre),$where);
            }
            $reponse = array('success' => 'Mise à jour réussie', 'message' => "Les données de l'utilisateur ont été modifiées", 'data' => array("ok" => "ok"));
        }
        if(isset($tab_user['profile_id'])) {

                // on supprime tous les droits pour les recreer ensuite
                $userRight = new UserRightFeature();
                $userRight->disableRelations(array('profile_id', 'feature_id'));
                $userRight->delete('user_id = '.$id);

                // on détermine les valeur qu'il faut mettre à jour
                // en faisant la différence avec les droits du profil
                $rightsP = new ProfileRightFeature();
                $rightsP->disableRelations(array('feature_id', 'profile_id'));

                foreach($rightsP->fetchAll('profile_id = '.$tab_user['profile_id']) as $profile) {
                    $row = $userRight->createRow();
                    $row->user_id = $id;
                    $row->feature_id = $profile->feature_id;
                    $row->right = 1;
                    $row->save();
                }
            }
        $this->_helper->json($reponse);

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

        $user = new User();
        $where = $user->getAdapter()->quoteInto('id = ?', $id);
        $result = $user->delete($where);

        $centre = new LinkUserCentre();
        $where = $centre->getAdapter()->quoteInto('id_user = ?', $id);
        $result = $centre->delete($where);

        $userRight = new UserRightFeature();
        $userRight->disableRelations(array('profile_id', 'feature_id'));
        $userRight->delete('user_id = '.$id);

        $reponse = array('success' => 'Suppression réussie', 'message' => "Les données de l'utilisateur ont été supprimées");
        $this->_helper->json($reponse);
    }
}