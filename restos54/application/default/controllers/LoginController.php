<?php
class LoginController extends Zend_Controller_Action
{
    protected $_flashMessenger;
    const MIN_CARACTERS_FOR_CREDENTIALS = 6;

    public function init()
    {
        parent::init();
        $this->_flashMessenger = $this->_helper->FlashMessenger;
    }
    
    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity())
        {
            $this->_redirect('/');
        }

        $this->view->message = $this->_flashMessenger->getCurrentMessages();

        if (!($this->_hasParam('login') && $this->_hasParam('password'))) {
            $form = new My_Form_Login();

            $this->view->formulaire = $form;
        }

        $this->view->doctype('XHTML1_TRANSITIONAL');

    }
    
    public function connecterAction()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity())
        {
            $this->_redirect('/');
        }
        
        if ($this->_request->isPost()) {
        
            $formData = $this->_request->getPost();
            $form = new My_Form_Login();
            if ($form->isValid($formData)) {

                $adapter  = new Zend_Auth_Adapter_DbTable($this->getFrontController()->getParam('db'), 'user', 'login', 'password');
                
                $adapter->setIdentity($this->_getParam('login'))
                    ->setCredential($this->_getParam('password'));
                    
                $result = $auth->authenticate($adapter);
                $userInfos = $adapter->getResultRowObject();
                
                if (!$result->isValid())
                {
                    $auth->clearIdentity();
                    $this->_flashMessenger->addMessage('authentification incorrecte');
                    $form->populate($this->_request->getPost());
                    $this->view->formulaire = $form;
                    $this->_forward('index');
                }else{
                    Zend_Session::regenerateId();
                    // enregistrement de l'objet user en session
                    $users = new User();
                    $user = $users->fetchRow($users->select()->where('login LIKE ?', $auth->getIdentity()));
                    $userSession = new Zend_Session_Namespace('User_Login');
                    $userSession->user = $user;
                    
                    $this->_redirect('/');
                }
                
            } else {
                $form->populate($this->_request->getPost());
                $this->view->formulaire = $form;
                $this->_flashMessenger->addMessage('authentification incorrecte');
                $this->_forward('index');
            }
        } else {
            $form->populate($this->_request->getPost());
            $this->view->formulaire = $form;
            $this->_flashMessenger->addMessage('authentification incorrecte');
            $this->_forward('index');
        }
    }
    
    public function deconnecterAction()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity())
        {
            $auth->clearIdentity();
            Zend_Session::forgetMe();
            Zend_Session::destroy();
        }
        $this->_redirect('/');
    }
}
