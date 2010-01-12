<?php
class ErrorController extends Zend_Controller_Action
{
    public function indexAction(){
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);
    }

    public function errorAction()
    {
        // on récupère les erreurs
        $errors = $this->_getParam('error_handler');

        // on initialise le loggueur
        $logger = new Zend_Log();
        Zend_Registry::get('logger')->write($errors);
        $userSession = new Zend_Session_Namespace('User_Login');

        // Ensuite, on récupère la réponse
        $response = $this->getResponse();
        $header_xml = array('name' => 'Content-Type', 'value' => 'text/xml', 'replace' => true);
        $header_json = array('name' => 'Content-Type', 'value' => 'application/json', 'replace' => true);
        // on définit le content-type original
        $contentType = 'html';
        if(in_array($header_xml, $response->getHeaders())) {
            $contentType = 'xml';
        }

        if(in_array($header_json, $response->getHeaders())) {
            $contentType = 'json';
        }
        // on redéfinit la vue car l'erreur provient peut etre d'un autre module
        $view = My_View::chooseView($this->getRequest());

        // selon le type d'erreur
        switch ($errors->type)
        {
            // si le controller ou l'action n'existe pas
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // on redirige vers la home
                $this->_redirect('/');
                break;

            // tout autre type d'erreur
            default:
                // on affiche une erreur standard
                $view->view->message = 'Une erreur est survenue, veuillez contacter un administrateur';
                $this->_forward($contentType);
                break;
        }
    }

    public function xmlAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml', true);
    }

    public function htmlAction()
    {
        $this->getResponse()->clearAllHeaders();
    }

    public function jsonAction()
    {
        // do some processing...
        // Send the JSON response:
        $exception = $this->getResponse()->getException();
        if($exception[0] instanceOf Zend_Db_Exception)
        {
            $message = "Problème avec la base de données";
        }
        else
        {
            $message = "Contactez un administrateur";
        }
        $reponse = array('success' => 'Erreur !', 'message' => $message, 'data' => array("erreur" => "erreur"));
        $this->_helper->json($reponse);
    }
}