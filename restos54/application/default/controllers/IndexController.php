<?php

class IndexController extends Zend_Controller_Action 
{
    function indexAction(){
        //$features = new Feature();
    }
    
    public function treeAction()
    {
        $node = $this->_request->getParam('node', null);
        
        $id = 0;
        if(isset($node)) {
            $id = (int)substr($node, 5, (strlen($node) - 5));
        }
        $this->view->json = Zend_Json::encode($this->createTree($id));
        //$this->_helper->json->sendJson(Zend_Json::encode($this->createTree($id)));
    }
    
    private function createTree($parent = null, $featureTitles = null)
    {

        $aclSession = new Zend_Session_Namespace('User_Acl');
        if(isset($aclSession->acl)) {
            $acl = $aclSession->acl;
        } else {
            return array();
        }
        
        if(is_null($parent) || $parent==0 ) {
            $where_parent = 'IS NULL';
        } else {
            $where_parent = '= '. $parent ;
        }
        
        $features = new Feature();
        $features->disableRelations(array('feature_type_id', 'parent_id'));
        $menus = $features->fetchAll(array('feature_type_id <= 2', 'parent_id '.$where_parent), 'order');
        
        $tab = array();
        foreach($menus as $menu) {
            $resourceName = '/' . $menu->module . 
                            '/' . $menu->controller . 
                            '/' . $menu->action;
            
            if($acl->has(new Zend_Acl_Resource($resourceName)) && $acl->isAllowed('user', $resourceName, null)) {

                $menuTitle = $menu->title;

                $childMenu = $this->createTree($menu->id, $featureTitles);
                if(count($childMenu)==0) {
                    $tab[] = array(
                            'id' => 'tree_' . $menu->id, 
                            'text' => $menuTitle,
                            'leaf' => true, 
                            'module' => $menu->module, 
                            'controller' => $menu->controller, 
                            'action' => $menu->action 
                            );
                } else {
                    $tab[] = array(
                            'id' => 'tree_' . $menu->id, 
                            'text' => $menuTitle,
                            'leaf' => false, 
                            'children' => $childMenu);
                }
            }
        }
        
        return $tab;
    }
    
    /**
     * Génère un JSON contenant les fonctionnalités pour la barre de navigation horizontale
     * @return void
     */
    public function menubarAction() {
        // récupère le noeud selectionné dans la barre de navigation verticale
        $node = $this->_request->getParam('node', null);
        
        $id = 0;
        if(isset($node)) {
            $id = (int)substr($node, 5, (strlen($node) - 5));
        }
        // récupère les noeuds enfant de facon structurée
        $data = $this->createMenuBar($id);
        $this->_helper->json->sendJson($data);
    }
    
    /**
     * Retourne un tableau contenant les fonctionnalités enfantes de la fonctionnalité $parent
     * @param integer $parent fonctionnalité parente, si null c'est la racine
     * @param array $featureTitles tableau avec les traductions des titres de fonctionnalité
     * @return array un tableau avec les noeuds enfants
     */
    private function createMenuBar($parent = null, $featureTitles = null)
    {

        // récupère les droits de l'utilisateur
        $aclSession = new Zend_Session_Namespace('User_Acl');
        $userSession = new Zend_Session_Namespace('User_Login');
        if(isset($aclSession->acl)) {
            $acl = $aclSession->acl;
        } else {
            return array();
        }
        
        // definition du critère sur le parent
        if(is_null($parent) || $parent==0 ) {
            $where_parent = 'IS NULL';
        } else {
            $where_parent = '= '. $parent ;
        }
        
        // Récupération des noeuds enfant (attention on ne prend pas que fonctionnalité de type groupe ou page
        $features = new Feature();
        $features->disableRelations(array('feature_type_id', 'parent_id'));
        $menus = $features->fetchAll(array('feature_type_id IN (3, 4)', 'parent_id '.$where_parent), 'order');


        $tab = array();
        // pour chaque fonctionnalité
        foreach($menus as $menu) {
            
            $tabAction = explode('/', $menu->action);
            // définition de la ressource
            $resourceName = '/' . $menu->module .
                            '/' . $menu->controller . 
                            '/' . $tabAction[0];

            // si l'utilisateur est autorisé à utilisé cette ressource ou si c'est un super utilisateur
            if(($acl->has(new Zend_Acl_Resource($resourceName)) && $acl->isAllowed('user', $resourceName, null)) || $userSession->user->su == 1) {

                // on regarde si on a une traduction pour le titre du menu
                $menuTitle = $menu->title;

                // on récupère les enfants de l'enfant
                $childMenu = $this->createMenuBar($menu->id, $featureTitles);
                // s'il n'y a pas d'enfant
                if(count($childMenu)==0) {
                    $tab[] = array(
                            'id' => 'menu_' . $menu->id, 
                            'text' => $menuTitle,
                            'group' => false,
                            'iconCls' => '',
                            'link' => '/' . $menu->module . '/' . $menu->controller . '/' . $menu->action
                            );
                }
                // s'il y a des enfants
                else {
                    $tab[] = array(
                            'id' => 'menu_' . $menu->id, 
                            'text' => $menuTitle,
                            'iconCls' => '',
                            'menu' => $childMenu, 
                            'group' => true 
                            );
                }
            }
        }
        return $tab;
    }

    //  ****************** TEST *******************************

    function checkboxtreeAction() {
        $features = new Feature();
        $features->disableRelations('parent_id');
        $allfeatures = array();
        foreach($features->fetchAll(array(), 'order') as $obj) {
            $allfeatures[] = $obj;
        }

        $this->view->features = $allfeatures;
        $this->view->json = Zend_Json::encode($this->getFeatures(0));
    }

    /*function listAction()
    {
        $node = $this->_request->getParam('node', null);

        $id = 0;
        if(isset($node)) {
            $id = (int)substr($node, 5, (strlen($node) - 5));
        }

        $this->view->json = Zend_Json::encode($this->getFeatures($id));
    }*/

    private function getFeatures($parent = null, $featureTitles = null)
    {

        // si les traductions des titres de fonctionnalité n'est pas défini, on le fait
        if(is_null($featureTitles)) {
            // création du tableau
            $featureTitles = array();
            // récupération de la langue de l'utilisateur
            $langSession = new Zend_Session_Namespace('User_Lang');

            $featureTitles = WordingFieldLanguage::getTranslationFields('bohd_feature', 'title', $langSession->langs->getselected());
        }

        if(is_null($parent) || $parent==0 ) {
            $where_parent = 'IS NULL';
        } else {
            $where_parent = '= '. $parent ;
        }

        $features = new Feature();
        $features->disableRelations(array('feature_type_id', 'parent_id'));
        $menus = $features->fetchAll(array('parent_id '.$where_parent), 'order');

        $tab = array();
        foreach($menus as $menu) {

            // on regarde si on a une traduction pour le titre du menu
            $menuTitle = (array_key_exists($menu->id, $featureTitles)?$featureTitles[$menu->id]:$menu->title);

            $parent = array(
                        'id' => 'tree_' . $menu->id,
                        'num' => $menu->id,
                        'text' => $menuTitle,
                        'module' => $menu->module,
                        'controller' => $menu->controller,
                        'action' => $menu->action,
                        'leaf' => true,
                        'checked' => false,
                        'cbxName' => 'cbx_features[]',
                        'cbxId' => 'cbx_'.$menu->id,
                        'cbxValue' => $menu->id
            );

            if(in_array($menu->id, array(12, 14, 2, 16, 17, 4, 19, 20))) {
                $parent['checked'] = true;
            }

            $childMenu = $this->getFeatures($menu->id, $featureTitles);
            if(count($childMenu) > 0) {
                $parent['leaf'] = false;
                $parent['children'] = $childMenu;
            }
            $tab[] = $parent;
        }
        return $tab;
    }

    // ************** fin test ***************************
}