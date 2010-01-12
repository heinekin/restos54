<?php
class Zend_View_Helper_Partiel
{
    function partiel($module=null, $directory=null, $tpl='')
    {
        if($tpl == '') {
            throw new Zend_Exception('Vous devez indiquer un fichier de template');
        }

        $base = ROOT_PATH . 'application' . DIRECTORY_SEPARATOR .
                'default' . DIRECTORY_SEPARATOR .
                'views' . DIRECTORY_SEPARATOR .
                'partial' . DIRECTORY_SEPARATOR;

        $partial = $base . (!is_null($module)?$module.DIRECTORY_SEPARATOR:'');
        $partial .= (!is_null($directory)?$directory.DIRECTORY_SEPARATOR:'');

        if(substr($tpl, -4) != '.tpl') {
            $tpl .= '.tpl';
        }

        $partial .= $tpl;

        return $partial;
    }
}