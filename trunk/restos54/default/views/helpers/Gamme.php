<?php
class Zend_View_Helper_Gamme
{
    function gamme()
    {
        $gamme = new ProductGamme();
        $result = $gamme->fetchAll();
        $tab = $result->toArray();
        $str = '[';
        foreach($tab as $c)
        {
            $str .= '[' . $c['id'] . ",'" . $c['gamme'] . "'],";
        }
        $str = substr($str,0,strlen($str)-1);
        $str .= ']';
        return $str;
    }
}