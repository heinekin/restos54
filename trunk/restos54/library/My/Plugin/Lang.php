<?php

class My_Plugin_Lang extends Zend_Controller_Plugin_abstract
{
    private $_langs = null;

    public function __construct()
    {
        $langSession = new Zend_Session_Namespace('User_Lang');
        if(isset($langSession->langs)) {
            $this->_langs = $langSession->langs;
        }
    }

    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $auth = Zend_Auth::getInstance();

        if($auth->hasIdentity() && !$this->_langs instanceof My_UserLangs) {
            $userSession = new Zend_Session_Namespace('User_Login');

            $usersLanguage = new UserLanguage();
            $usersLanguage->disableRelations('user_id');
            
            $this->_langs = new My_UserLangs();
            foreach($usersLanguage->fetchAll('user_id = '.$userSession->user->id) as $language) {
                $lg = new My_Lang($language->language_id->id, $language->language_id->language, $language->language_id->title, $language->language_id->admin);
                $this->_langs->pushAll($lg);
                if($language->main == 1) {
                    $this->_langs->setSelected($lg);
                }
            }

            $langSession = new Zend_Session_Namespace('User_Lang');
            $langSession->langs = $this->_langs;
        }
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $auth = Zend_Auth::getInstance();

        // si la personne est identifié
        if($auth->hasIdentity()) {

            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');

            $viewRenderer->view->allLangs = $this->_langs->getAllAdmin();
            
            $viewRenderer->view->selectedLang = $this->_langs->getSelected()->getId();
        }
        
    }
}
 
