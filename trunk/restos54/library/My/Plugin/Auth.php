<?php
class My_Plugin_Auth extends Zend_Controller_Plugin_abstract
{
    private $_auth;
    
    public function __construct()
    {
        $this->_auth = Zend_Auth::getInstance();
    }
    
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        if(!$this->_auth->hasIdentity()) {
            if(!strpos($this->getRequest()->getRequestUri(), 'login/')) {
                $this->getRequest()->setRequestUri('/login/');
            }
        }
    }
    
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $userSession = new Zend_Session_Namespace('User_Login');
        $viewRenderer->view->login_user = $userSession->user;
    }
}