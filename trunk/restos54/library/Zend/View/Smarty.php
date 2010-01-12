<?php
require_once 'Zend/View/Interface.php';
require_once 'Smarty.class.php';

class Zend_View_Smarty extends Zend_View_Abstract
{
    /**
     * Objet Smarty
     * @var Smarty
     */
    protected $_smarty;

    /**
     * Constructor
     *
     * @param string $tmplPath
     * @param array $extraParams
     * @return void
     */
    public function __construct($tmplPath = null, $extraParams = array())
    {
        $this->_smarty = new Smarty;

        if (null !== $tmplPath) {
            $this->setScriptPath($tmplPath);
        }

        foreach ($extraParams as $key => $value) {
            $this->_smarty->$key = $value;
        }

        // user-defined helper path
        if (array_key_exists('helperPath', $extraParams)) {
            $prefix = 'Zend_View_Helper';
            if (array_key_exists('helperPathPrefix', $extraParams)) {
                $prefix = $extraParams['helperPathPrefix'];
            }
            $this->addHelperPath($extraParams['helperPath'], $prefix);
        }

        $this->_smarty->assign('this', $this);
    }

    /**
     * Retourne l'objet moteur de gabarit
     *
     * @return Smarty
     */
    public function getEngine()
    {
        return $this->_smarty;
    }

    /**
     * Affecte le dossier des scripts de gabarits
     *
     * @param string $path Le r�pertoire � affecter au path
     * @return void
     */
    public function setScriptPath($path)
    {
        if (is_readable($path)) {
            $this->_smarty->template_dir = $path;
            return;
        }

        throw new Exception('Directory invalid !');
    }

    /**
     * R�cup�re le dossier courant des gabarits
     *
     * @return string
     */
    public function getScriptPaths()
    {
        return array($this->_smarty->template_dir);
    }

    /**
     * Alias pour setScriptPath
     *
     * @param string $path
     * @param string $prefix Unused
     * @return void
     */
    public function setBasePath($path, $prefix = 'Zend_View')
    {
        return $this->setScriptPath($path);
    }

    /**
     * Alias pour setScriptPath
     *
     * @param string $path
     * @param string $prefix Unused
     * @return void
     */
    public function addBasePath($path, $prefix = 'Zend_View')
    {
        return $this->setScriptPath($path);
    }

    /**
     * Affectation une variable au gabarit
     *
     * @param string $key Le nom de la variable
     * @param mixed $val La valeur de la variable
     * @return void
     */
    public function __set($key, $val)
    {
        $this->_smarty->assign($key, $val);
    }

    /**
     * Recherche une variable au gabarit
     *
     * @param string $key Le nom de la variable
     * @return mixed La valeur de la variable
     */
    public function __get($key)
    {
        return $this->_smarty->get_template_vars($key);
    }

    /**
     * Autorise le fonctionnement du test avec empty() and isset()
     *
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return (null !== $this->_smarty->get_template_vars($key));
    }

    /**
     * Autorise l'effacement de toutes les variables du gabarit
     *
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        $this->_smarty->clear_assign($key);
    }

    /**
     * Affectation de variables au gabarit
     *
     * Autorise une affectation simple (une cl� => une valeur) 
     * OU 
     * le passage d'un tableau (paire de cl� => valeur) � affecter en masse
     *
     * @see __set()
     * @param string|array $spec Le type d'affectation � utiliser (cl� ou tableau de paires cl� => valeur)
     * @param mixed $value (Optionel) Si vous assignez une variable nomm�e, utilis� ceci comme valeur
     * @return void
     */
    public function assign($spec, $value = null)
    {
        if (is_array($spec)) {
            $this->_smarty->assign($spec);
            return;
        }

        $this->_smarty->assign($spec, $value);
    }

    /**
     * Effacement de toutes les variables affect�es
     *
     * Efface toutes les variables affect�es � Zend_View via {@link assign()} ou
     * surcharge de propri�t� ({@link __get()}/{@link __set()}).
     *
     * @return void
     */
    public function clearVars()
    {
        $this->_smarty->clear_all_assign();
        /* On reassigne le this, (object View) */
        $this->assign('this', $this);
    }

    /**
     * Ex�cute le gabarit et retourne l'affichage
     *
     * @param string $name Le gabarit � ex�cuter
     * @return string L'affichage
     */
    public function render($name)
    {
        return $this->_smarty->fetch($name);
    }

    /**
     * Registers a resource to fetch a template
     *
     * @param string $type name of resource
     * @param array $functions array of functions to handle resource
     */
    public function register_resource($type, $functions)
    {
        $this->_smarty->register_resource($type, $functions);
    }

    /**
     * executes & displays the template results
     *
     * @param string $resource_name
     * @param string $cache_id
     * @param string $compile_id
     */
    function display($resource_name, $cache_id = null, $compile_id = null)
    {
        $this->_smarty->fetch($resource_name, $cache_id, $compile_id, true);
    }

    public function _run()
    {
    }
}
