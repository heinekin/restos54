<?php

class ad54_FeatureController extends Zend_Controller_Action
{
    function indexAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);
    }

    function addAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);

        $form = new My_Form_Feature();
        $form->setAction('/ad54/feature/add');
        $form->submit->setLabel('ajouter');
        $this->view->formulaire = $form;

        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            if ($form->isValid($formData)) {

                $allSubForms = $form->getSubForms();

                $feature = new Feature();
                $row = $feature->createRow();
                $row->code = $form->getValue('code');
                $row->title = $form->getValue('title');
                $row->module = $form->getValue('module');
                $row->controller = $form->getValue('controller');
                $row->action = $form->getValue('page');
                $row->order = $form->getValue('order');
                $row->feature_type_id = $form->getValue('featureType');
                $row->parent_id = ( $form->getValue('featureParent') == 0 ? null : $form->getValue('featureParent'));
                $id_feature = $row->save();

                if($id_feature > 0) {
                    $profileRights = new ProfileRightFeature();
                    $profileRights->disableRelations(array('profile_id', 'feature_id'));
                    if(is_array($allSubForms['subform']->getValue('profileRight'))) {
                        foreach($allSubForms['subform']->getValue('profileRight') as $profile)
                        {
                            $row = $profileRights->createRow();
                            $row->profile_id = $profile;
                            $row->feature_id = $id_feature;
                            $row->save();
                        }
                    }
                   // unvalidate the recursive parents
                   $profileRights->disableRecursiveParent($id_feature);
                }

                $this->_forward('index');
            } else {
                $form->populate($formData);
                $this->view->errorFields = $form->getFieldsErrors();
                $this->view->flagError = true;
            }
        }
    }

    function modifyAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);

        $form = new My_Form_Feature();
        $form->setAction('/ad54/feature/modify');
        $form->submit->setLabel('modifier');
        $this->view->formulaire = $form;

        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            if ($form->isValid($formData)) {

                $allSubForms = $form->getSubForms();
                
                $feature = new Feature();
                $row = $feature->fetchRow('id='.$form->getValue('id'));

                $feature->unload($row);

                $row->code = $form->getValue('code');
                $row->title = $form->getValue('title');
                $row->module = $form->getValue('module');
                $row->controller = $form->getValue('controller');
                $row->action = $form->getValue('page');
                $row->order = $form->getValue('order');
                $row->feature_type_id = $form->getValue('featureType');
                $row->parent_id = ( $form->getValue('featureParent') == 0 ? null : $form->getValue('featureParent'));

                $row->save();

                $profileRights = new ProfileRightFeature();
                $profileRights->disableRelations(array('profile_id', 'feature_id'));
                // on supprime tous les droits associés à la feature
                
                $profileRights->delete('feature_id='.$form->getValue('id'));
                foreach($allSubForms['subform']->getValue('profileRight') as $profile)
                {
                    $row = $profileRights->createRow();
                    $row->profile_id = $profile;
                    $row->feature_id = $form->getValue('id');
                    $row->save();
                }

                $this->_forward('index');

            } else {
                $form->populate($formData);
                $this->view->errorFields = $form->getFieldsErrors();
                $this->view->flagError = true;
            }
        } else {

            $id_feature = $this->_request->getParam('id');

            $feature = new Feature();
            $feature->disableRelations(array('feature_type_id', 'parent_id'));

            $rows = $feature->find($id_feature);
            if($rows->count() > 0) {

                $row = $rows->current();

                $tab['id'] = $row->id;
                $tab['code'] = $row->code;
                $tab['title'] = $row->title;
                $tab['module'] = $row->module;
                $tab['controller'] = $row->controller;
                $tab['page'] = $row->action;
                $tab['order'] = $row->order;
                $tab['featureType'] = $row->feature_type_id;
                $tab['featureParent'] = $row->parent_id;

                $profileRights = new ProfileRightFeature();
                $profileRights->disableRelations(array('profile_id', 'feature_id'));
                $profileRight = $profileRights->fetchAll('feature_id=' . $row->id);

                $profile_allowed = array();
                foreach($profileRight as $obj) {
                    $profile_allowed[] = $obj->profile_id;
                }
                $tab['profileRight'] = $profile_allowed;

                $form->populate($tab);
            } else {
                $this->_forward('index');
            }
        }
    }

    function removeAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);

        $id_feature = $this->_request->getParam('id');
        if($id_feature > 0) {
            $feature = new Feature();
            $feature->disableRelations(array('feature_type_id', 'parent_id'));
            if($feature->delete('id = '.$id_feature) > 0) {

                $this->view->flagError = false;
                $this->view->idnode = $id_feature;

                $profileRight = new ProfileRightFeature();
                $profileRight->disableRelations(array('profile_id', 'feature_id'));
                $profileRight->delete('feature_id = '.$id_feature);

                $userRight = new UserRightFeature();
                $userRight->disableRelations(array('user_id', 'feature_id'));
                $userRight->delete('feature_id = '.$id_feature);

            } else {
                $this->view->flagError = false;
                $this->view->messageError = 'La suppression a echou�e';
            }
        }
    }

    function listAction()
    {
        $node = $this->_request->getParam('node', null);

        $id = 0;
        if(isset($node)) {
            $id = (int)substr($node, 5, (strlen($node) - 5));
        }

        $this->view->json = Zend_Json::encode($this->getFeatures($id));
    }

    private function getFeatures($parent = null)
    {

        // si les traductions des titres de fonctionnalité n'est pas défini, on le fait
        /*if(is_null($featureTitles)) {
            // création du tableau
            $featureTitles = array();
            // récupération de la langue de l'utilisateur
            $langSession = new Zend_Session_Namespace('User_Lang');

            $featureTitles = WordingFieldLanguage::getTranslationFields('bohd_feature', 'title', $langSession->langs->getselected());
        }*/

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
            $parent = array(
                        'id' => 'tree_' . $menu->id,
                        'num' => $menu->id,
                        'text' => $menuTitle,
                        'module' => $menu->module,
                        'controller' => $menu->controller,
                        'action' => $menu->action,
                        'leaf' => $leaf
            );
            $tab[] = $parent;
        }
        return $tab;
    }
}