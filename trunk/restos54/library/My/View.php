<?php
require_once 'functions/smarty_id_resource.php';
require_once 'functions/smarty_code_resource.php';

class My_View {
    
    public static function chooseView(Zend_Controller_Request_Abstract $request) {
        
        $compile_dir = '../data/templates_c/';
        // si le répertoire de compilation du template n'existe pas, on tente de le créer
        if(!file_exists(realpath($compile_dir) . DIRECTORY_SEPARATOR . $request->getModuleName()))
        {
            // si la création ne fonctionne pas
            if(!mkdir(realpath($compile_dir) . DIRECTORY_SEPARATOR . $request->getModuleName(), 0777))
            {
                // on lève une exception
                throw new Zend_Exception('Impossible de créer le répertoire de compilation : ' .
                    realpath($compile_dir) . DIRECTORY_SEPARATOR . $request->getModuleName()
                );
            }
        }

        $params = array(
            'template_dir' => '../application/' . $request->getModuleName() . '/views/scripts',
            'compile_dir' => $compile_dir . $request->getModuleName()
        );

        $pathsHelpers = array('../application/default/views/helpers/');
        if($request->getModuleName() != 'default') {
            $pathsHelpers[] = '../application/' . $request->getModuleName() . '/views/helpers/';
        }
        $view = new Zend_View_Smarty(null, $params);
        $view->addHelperPath($pathsHelpers);

        $view->register_resource('id', array('id_get_template',
                                       'id_get_timestamp',
                                       'id_get_secure',
                                       'id_get_trusted'));
        $view->register_resource('code', array('code_get_template',
                                       'code_get_timestamp',
                                       'code_get_secure',
                                       'code_get_trusted'));

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);
        
        return $viewRenderer;
        
    }
}