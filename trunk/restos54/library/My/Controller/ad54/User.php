<?php
class My_Controller_ad54_User extends Zend_Controller_Action {

    public function indexAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);

    }
    public function listAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);

    }
    public function rightAction() {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);

        // si on a recu des elements en POST
        if($this->_request->isPost()) {
            $formData = $this->_request->getPost();

            if($formData['id'] > 0 && $formData['profile_id']) {

                // on détermine les valeur qu'il faut mettre à jour
                // en faisant la différence avec les droits du profil
                $rightsP = new ProfileRightFeature();
                $rightsP->disableRelations(array('feature_id', 'profile_id'));

                foreach($rightsP->fetchAll('profile_id = '.$formData['profile_id']) as $row) {
                    $rightProfil['right_'.$row->feature_id] = 1;
                }

                // on supprime tous les droits pour les recreer ensuite
                $userRight = new UserRightFeature();
                $userRight->disableRelations(array('profile_id', 'feature_id'));
               $userRight->delete('user_id = '.$formData['id']);
               

                $data = array();
                foreach($formData as $k => $v) {
                    if(substr($k, 0, 6)=='right_'){
                        list(,$idFeature) = explode('_', $k);
                            $row = $userRight->createRow();
                            $row->user_id = $formData['id'];
                            $row->feature_id = $idFeature;
                            $row->right = $formData[$k];
                            $row->save(); 
                    }
                }
            }

            $this->_forward('list');

        } else {
            $idUser = $this->_request->getParam('id');
            if($idUser > 0) {

                // on récupère l'id du profil associ�
                $users = new User();
                $row = $users->fetchRow('id = '.$idUser);
                if(is_null($row->profile_id))
                    $idProfile = '0';
                else
                    $idProfile = $row->profile_id->id;

                $default = array();
                $default['id'] = $idUser;
                $default['profile_id'] = $idProfile;

                // on récupère d'abord les droits du profil
                $rightsP = new ProfileRightFeature();
                $rightsP->disableRelations(array('feature_id', 'profile_id'));

                foreach($rightsP->fetchAll('profile_id = '.$idProfile) as $row) {
                    $default['right_'.$row->feature_id] = 1;
                }

                // on récupère ensuite les droits du user
                $rightsU = new UserRightFeature();
                $rightsU->disableRelations(array('user_id', 'feature_id'));

                foreach($rightsU->fetchAll('user_id = '.$idUser) as $row) {
                    $default['right_'.$row->feature_id] = $row->right;
                }
               

                // on affiche le tableau
                $form = new My_Form_RightUser(false);
                $form->setAction('/ad54/user/right');
                $form->submit->setLabel('modifier');
                $form->populate($default);
                $this->view->formulaire = $form;
            }
        }


    }
}