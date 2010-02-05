<?php

class ad54_ProfileController extends Zend_Controller_Action 
{

    function indexAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);

        $profiles = new Profile();
        $rows = $profiles->fetchAll();

        $this->view->dataProfile = $rows->toArray();
    }
    
    function modifyAction()
    {
        $tab = array();
        if ($this->_request->isPost()) {
            
            $id = $this->_request->getParam('id');
            $code = $this->_request->getParam('code');
            
            $tab['id'] = $id;
            
            $profiles = new Profile();
            $row = $profiles->fetchRow('id='.$id);
            
            $row->id = $id;
            $row->code = $code;
            try
            {
                if($row->save() == 0) {
                    $tab['state'] = 'ko';
                } else {
                    $tab['state'] = 'ok';
                }
            } catch(Exception $e) {
                $tab['state'] = 'ko';
            }
        } else {
            $tab['id'] = 0;
            $tab['state'] = 'ko';
        }
        
        $this->_helper->json->sendJson(Zend_Json::encode($tab));
    }
    
    function deleteAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);
        $this->view->id_profile = 0;
        
        $idProfile = $this->_request->getParam('id');
        if($idProfile > 0) {
            
            try{
                $profile = new Profile();
                $profile->delete('id = '.$idProfile);
                
                $profileRight = new ProfileRightFeature();
                $profileRight->disableRelations(array('profile_id', 'feature_id'));
                $profileRight->delete('profile_id = '.$idProfile);
                
                $this->view->id_profile = $idProfile;
            } catch(Exception $e) {}
        }
    }
    
    function rightAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);
            $idProfile = $this->_request->getParam('id');

            $profiles = new Profile();
            $row = $profiles->fetchRow('id = ' . $idProfile);
            $this->view->codeProfile = $row->code;
            $this->view->profileId = $row->id;
    }

    function listAction($profile_id=null)
    {
        $node = $this->_request->getParam('node', null);
        $profile_id = $this->_request->getParam('id',null);

        $id = 0;
        if(isset($node)) {
            $id = (int)substr($node, 5, (strlen($node) - 5));
        }
        $this->view->json = Zend_Json::encode($this->getRights($profile_id,$id));
    }

    private function getRights($profile_id, $parent = null, $featureTitles = null)
    {
/*
        // si les traductions des titres de fonctionnalité n'est pas défini, on le fait
        if(is_null($featureTitles)) {
            // création du tableau
            $featureTitles = array();
            // récupération de la langue de l'utilisateur
            $langSession = new Zend_Session_Namespace('User_Lang');

            $featureTitles = WordingFieldLanguage::getTranslationFields('bohd_feature', 'title', $langSession->langs->getselected());
        }
*/
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
            $menuTitle = $menu->title;
            // get the tree to be dynamically expandable
            if ($features->isLeaf($menu['id'])) {
                $leaf = false;
            } else {
                $leaf = true;
            }

            $myRights = new ProfileRightFeature();
            $isRecursive = false;
            $clicState = 0;
            $isChecked = false;
            $treeMode = $myRights->hasRights($menu['id'], $profile_id);
            if ($treeMode) {
                $isChecked = true;
                $clicState = 1;
                if ($treeMode['recursive']) {
                    $isRecursive = true;
                    $clicState = 2;
                }
            } 
            $parent = array(
                        'id' => 'tree_' . $menu->id,
                        'num' => $menu->id,
                        'text' => $menuTitle,
                        'module' => $menu->module,
                        'controller' => $menu->controller,
                        'action' => $menu->action,
                        'leaf' => $leaf,
                        'checked' => $isChecked,
                        'clicState' => $clicState,
                        'cbxName' => 'cbx_features[]',
                        'cbxId' => 'cbx_'.$menu->id,
                        'cbxValue' => $menu->id,
                        'isRecursive' => $isRecursive
            );
            $tab[] = $parent;
        }
        return $tab;
    }


    function addAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);
        
        $form = new My_Form_Profile(true);
        $form->setAction('/profile/add');
        $form->submit->setLabel('Ajouter');
        $this->view->formulaire = $form;
        
        if($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            
            if ($form->isValid($formData)) {
                
                $profile = new Profile();
                $row = $profile->createRow();
                $row->code = $form->getValue('code');
                $profile_id = $row->save();
                if($profile_id > 0) {
                    $profileRight = new ProfileRightFeature();
                    $profileRight->disableRelations(array('profile_id', 'feature_id'));
                    foreach($form->getValue('profileRight') as $feature_id) {
                        $row = $profileRight->createRow();
                        $row->profile_id = $profile_id;
                        $row->feature_id = $feature_id;
                        $row->save();
                    }
                }
                
                $this->_forward('index');
            } else {
                $form->populate($formData);
                $this->view->errorFields = $form->getFieldsErrors();
                $this->view->flagError = true;
            }
        }
        
    }

    private function _enableRecursiveChildren($profile_id, $feature_id) {
        $profileRights = new ProfileRightFeature();
        $profileRights->disableAllRelations();
        $children = $profileRights->getRecursiveChildren($feature_id);

        // insert the children features to see the checkbox in the tree view
        foreach($children as $childFeature) {
            $data['profile_id'] = $profile_id;
            $data['feature_id'] = $childFeature;
            $data['recursive'] = 1;
            // a leaf cannot be recursive
            if ((sizeof($profileRights->getRecursiveChildren($childFeature))==0)) $data['recursive'] = 0;
            if (!$profileRights->fetchRow("profile_id = " . $profile_id . " AND feature_id = " . $childFeature)) $profileRights->createRow($data)->save();
        }
        return $children;
    }

    function updateAction() {
        $this->_helper->viewRenderer->setNoRender();
        $profile_id = $this->_request->getParam('profile_id',null);
        $features = json_decode("[" . substr($this->_request->getParam('features',null),0,-1) . "]",true);
        $del_features = json_decode("[" . substr($this->_request->getParam('del_features',null),0,-1) . "]",true);
        unset($del_features[0]);

        $profileRights = new ProfileRightFeature();
        $profileRights->disableAllRelations();

        //delete all unchecked features
        foreach($del_features as $feature) {
            $profileRights->delete('profile_id = ' . $profile_id . ' AND feature_id = ' . $feature['id']);
        }

        // and add all the checked ones
        foreach($features as $feature) {
            $data['recursive'] = 0;
            if ($feature['recursive']) $data['recursive'] = 1;
            echo $data['recursive'];
            $data['profile_id'] = $profile_id;
            $data['feature_id'] = $feature['id'];
            // is this recursive ?
            if (($feature['recursive'])) {
                $this->_enableRecursiveChildren($profile_id,$feature['id']);
            } else {
                $profileRights->delete('profile_id = ' . $profile_id . ' AND feature_id = ' . $feature['id']);
            }
            if (!$profileRights->fetchRow("profile_id = " . $profile_id . " AND feature_id = " . $feature['id'])) $profileRights->createRow($data)->save();
        }
     
    }
}