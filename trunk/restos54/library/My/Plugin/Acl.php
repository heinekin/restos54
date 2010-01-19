<?php
class My_Plugin_Acl extends Zend_Controller_Plugin_abstract
{
    private $_acl;
    
    public function __construct()
    {
        $aclSession = new Zend_Session_Namespace('User_Acl');
        if(isset($aclSession->acl)) {
            $this->_acl = $aclSession->acl;
        }
    }
    
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $auth = Zend_Auth::getInstance();

        if($auth->hasIdentity() && !$this->_acl instanceof Zend_Acl) {
            $userSession = new Zend_Session_Namespace('User_Login');
            //Zend_Debug::dump($userSession->user);
            $this->_acl = new Zend_Acl();
            
            // creation du role user
            $this->_acl->addRole(new Zend_Acl_Role('user'));
            
            $userRights = new UserRightFeature();
            $u = (array)$userRights->fetchRightFeatures($userSession->user->id);
            
            foreach($u as $obj) {
                
                $tabAction = explode('/', $obj['action']);
                $resourceName = '/' . $obj['module'] . '/' . $obj['controller'] . '/' . $tabAction[0];

                if(!$this->_acl->has(new Zend_Acl_Resource($resourceName))) {
                    $this->_acl->add(new Zend_Acl_Resource($resourceName));
                }
                if($obj['right']) {
                    $this->_acl->allow('user', $resourceName);
                } else {
                    $this->_acl->deny('user', $resourceName);
                }
                
            }
            $aclSession = new Zend_Session_Namespace('User_Acl');
            $aclSession->acl = $this->_acl;
        }
    }
    
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity() && $this->_acl instanceof Zend_Acl) {

            // si ce n'est pas un super-utilisateur, on regarde s'il a le droit d'afficher la page
            $userSession = new Zend_Session_Namespace('User_Login');
            if($userSession->user->su == 0 && $this->getRequest()->getModuleName() != 'default') {

                $resourceName = '/' . $this->getRequest()->getModuleName() .
                                '/' . $this->getRequest()->getControllerName() .
                                '/' . $this->getRequest()->getActionName();

                if(!$this->_acl->has(new Zend_Acl_Resource($resourceName)) || !$this->_acl->isAllowed('user', $resourceName, null)) {

                    $this->getRequest()->setModuleName('default');
                    $this->getRequest()->setControllerName('error');
                    $this->getRequest()->setActionName('index');

                    $viewRenderer = My_View::chooseView($this->getRequest());
                    $viewRenderer->view->message = 'Acces Interdit';
                }
            }
        }
    }
}