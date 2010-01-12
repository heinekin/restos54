<?php
/**
 * My new Zend Framework project
 *
 * @author
 * @version
 */

require_once 'Zend/Controller/Plugin/Abstract.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Request/Abstract.php';
require_once 'Zend/Controller/Action/HelperBroker.php';
require_once 'Zend/Loader/Autoloader.php';

/**
 *
 * Initializes configuration depndeing on the type of environment
 * (test, development, production, etc.)
 *
 * This can be used to configure environment variables, databases,
 * layouts, routers, helpers and more
 *
 */
class My_Initializer extends Zend_Controller_Plugin_Abstract {
 /**
  * @var Zend_Config
  */
 protected $_config;

 /**
  * @var string Current environment
  */
 protected $_env;

 /**
  * @var Zend_Controller_Front
  */
 protected $_front;

 /**
  * @var string Path to application root
  */
 protected $_root;

  /**
  * @var Zend_Registry registry
  */
 protected $_registry;

 /**
  * Constructor
  *
  * Initialize environment, root path, and configuration.
  *
  * @param  string $env
  * @param  string|null $root
  * @return void
  */
 public function __construct($env, $root = null) {
  // Set up autoload.
  $autoloader = Zend_Loader_Autoloader::getInstance();
  $autoloader->setFallbackAutoloader(true);
  $this->_setEnv ( $env );
  if (null === $root) {
   $root = realpath ( dirname ( __FILE__ ) . '/../' );
  }
  $this->_root = $root;

  $this->initConfig ();

  $this->_front = Zend_Controller_Front::getInstance ();

  // set the test environment parameters
  if ($this->_config->display_error)
  {
    // Enable all errors so we'll know when something goes wrong.
    error_reporting ( E_ALL | E_STRICT );
    ini_set ( 'display_startup_errors', 1 );
    ini_set ( 'display_errors', 1 );
    //$this->_front->throwExceptions ( true );
  }
  else
  {
    ini_set ( 'display_startup_errors', 0 );
    ini_set ( 'display_errors', 0 );
    $this->_front->setParam("disableOutputBuffering",true);
  }

  $logger = new My_Log_Firebug($this->_config->display_error);
  $this->_registry->set('logger', $logger);
 }

 /**
  * Initialize environment
  *
  * @param  string $env
  * @return void
  */
 protected function _setEnv($env) {
  $this->_env = $env;
 }

 /**
  * Initialize Config
  *
  * @return void
  */
 public function initConfig() {
    $this->_config = new Zend_Config_Ini($this->_root. DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.ini', $this->_env);
    $this->_registry = Zend_Registry::getInstance();
    $this->_registry->set('config', $this->_config);
 }

 public function getRoot(){
  return $this->_root;
 }

 /**
  * Route startup
  *
  * @return void
  */
 public function routeStartup(Zend_Controller_Request_Abstract $request) {
  $this->initCache ();
  $this->initDb ();
  $this->initView ();
  //$this->initPlugins ();
  $this->initRoutes ();
  $this->initControllers ();
 }

 /**
   * Initialize data bases
  *
  * @return void
  */
 public function initDb()
 {
    $db = Zend_Db::factory($this->_config->resources->db);

    Zend_Db_Table::setDefaultAdapter($db);

    $db->query('SET NAMES UTF8');

    $this->_registry->set('db', $db);
    $this->_front->setParam('db', $db);

    if ($this->_config->db_profiling)
    {
        $profiler_gen = new Zend_Db_Profiler_Firebug('Db General');
        $profiler_gen->setEnabled(true);

        Zend_Db_Table_Abstract::getDefaultAdapter()->setProfiler($profiler_gen);
    }
    Zend_Db_Table_Abstract::setDefaultMetadataCache($this->_registry->get('MyCache'));
 }

 /**
  * Initialize cache
  *
  * @return void
  */
 public function initCache() {
    $frontendOptions = array('lifetime'=>60*60, 'automatic_serialization'=>true, 'caching'=>(bool)$this->_config->cache);
    $backendOptions = array('cache_dir' => CACHE_DIR);
    $MyCache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
    $this->_registry->set('MyCache', $MyCache);
 }

 /**
  * Initialize view
  *
  * @return void
  */
 public function initView() {
    $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
    $viewRenderer->setViewSuffix('tpl');
    Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
 }
  /**
  * Initialize plugins
  *
  * @return void
  */
 public function initPlugins() {
    /*$this->_front->registerPlugin(new My_Plugin_Auth(), 2);
    $this->_front->registerPlugin(new My_Plugin_Module(), 3);
    $this->_front->registerPlugin(new My_Plugin_Acl(), 4);
    $this->_front->registerPlugin(new My_Plugin_Lang(), 5);*/
 }

 /**
  * Initialize routes
  *
  * @return void
  */
 public function initRoutes() {
    $restRoute = new Zend_Rest_Route($this->_front, array(), array(
        'ad54' => array('rest','restproduct','restcentre','restbnf','resttypeproduct','restgammeproduct','restqprev')
    ));
    $this->_front->getRouter()->addRoute('rest', $restRoute);

 }

 /**
  * Initialize Controller paths
  *
  * @return void
  */
 public function initControllers() {
    $this->_front->setBaseUrl('/');
    $this->_front->setControllerDirectory(array(
          'default' => '../application/default/controllers',
          'ad54'    => '../application/ad54/controllers',
          'centres'    => '../application/centres/controllers'
    ));
  }
}
?>