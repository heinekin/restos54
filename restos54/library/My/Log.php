<?php
class My_Log {

    /*private static $_logger;
    private static $_channel;
    private static $_request;
    private static $_response;

    public static function getInstance() {

    }

    public static function init()
    {
        self::$_logger = new Zend_Log( new Zend_Log_Writer_Firebug() );

        self::$_request = new Zend_Controller_Request_Http();

        self::$_response = new Zend_Controller_Response_Http();

        self::$_channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        self::$_channel->setRequest($request);
        self::$_channel->setResponse($response);

        $registry = Zend_Registry::getInstance();
        $registry->set('firebug_log', $config);

    }*/

    public static function info($msg) {

        $registry = Zend_Registry::getInstance();
        $configIni = $registry->get('config');

        if($configIni->debug) {

            $writer = new Zend_Log_Writer_Firebug();
            $logger = new Zend_Log($writer);

            $request = new Zend_Controller_Request_Http();
            $response = new Zend_Controller_Response_Http();
            $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
            $channel->setRequest($request);
            $channel->setResponse($response);

            // Démarrer l'output buffering
            ob_start();

            // Maintenant vous pouvez appeler le logguer
            $logger->log(strval($msg), Zend_Log::INFO);

            // Envoi des données de journalisation vers le navigateur
            $channel->flush();
            $response->sendHeaders();

            return true;

        }else {
            return false;
        }
    }

    public static function warn($msg) {

        $registry = Zend_Registry::getInstance();
        $configIni = $registry->get('config');

        if($configIni->debug) {

            $writer = new Zend_Log_Writer_Firebug();
            $logger = new Zend_Log($writer);

            $request = new Zend_Controller_Request_Http();
            $response = new Zend_Controller_Response_Http();
            $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
            $channel->setRequest($request);
            $channel->setResponse($response);

            // Démarrer l'output buffering
            ob_start();

            // Maintenant vous pouvez appeler le logguer
            $logger->log(strval($msg), Zend_Log::WARN);

            // Envoi des données de journalisation vers le navigateur
            $channel->flush();
            $response->sendHeaders();

            return true;

        }else {
            return false;
        }
    }

    public static function error($msg) {

        $registry = Zend_Registry::getInstance();
        $configIni = $registry->get('config');

        if($configIni->debug) {

            $writer = new Zend_Log_Writer_Firebug();
            $logger = new Zend_Log($writer);

            $request = new Zend_Controller_Request_Http();
            $response = new Zend_Controller_Response_Http();
            $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
            $channel->setRequest($request);
            $channel->setResponse($response);

            // Démarrer l'output buffering
            ob_start();

            // Maintenant vous pouvez appeler le logguer
            $logger->log(strval($msg), Zend_Log::ERR);

            // Envoi des données de journalisation vers le navigateur
            $channel->flush();
            $response->sendHeaders();

            return true;

        }else {
            return false;
        }
    }

    public static function table(array $msg) {

        $registry = Zend_Registry::getInstance();
        $configIni = $registry->get('config');

        if($configIni->debug) {

            $writer = new Zend_Log_Writer_Firebug();
            $writer->setPriorityStyle(8, 'TABLE');
            $logger = new Zend_Log($writer);
            $logger->addPriority('TABLE', 8);

            $request = new Zend_Controller_Request_Http();
            $response = new Zend_Controller_Response_Http();
            $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
            $channel->setRequest($request);
            $channel->setResponse($response);

            // Démarrer l'output buffering
            ob_start();

            // Maintenant vous pouvez appeler le logguer
            $logger->table($msg);

            // Envoi des données de journalisation vers le navigateur
            $channel->flush();
            $response->sendHeaders();

            return true;

        }else {
            return false;
        }
    }

    public static function dump($msg, $label=null) {

        $registry = Zend_Registry::getInstance();
        $configIni = $registry->get('config');

        if($configIni->debug) {

            $writer = new Zend_Log_Writer_Firebug();
            $logger = new Zend_Log($writer);

            $request = new Zend_Controller_Request_Http();
            $response = new Zend_Controller_Response_Http();
            $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
            $channel->setRequest($request);
            $channel->setResponse($response);

            // Démarrer l'output buffering
            ob_start();

            // Maintenant vous pouvez appeler le logguer
            $dump = html_entity_decode(strip_tags(Zend_Debug::dump($msg, $label, false)));
            $logger->log($dump, Zend_Log::DEBUG);

            // Envoi des données de journalisation vers le navigateur
            $channel->flush();
            $response->sendHeaders();

            return true;

        }else {
            return false;
        }
    }
}

