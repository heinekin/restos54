<?php
class Zend_View_Helper_Centres
{
    function centres()
    {
        $centre = new Centre();
        $result = $centre->fetchAll();
        $tab = $result->toArray();
        $str = '[';
        foreach($tab as $c)
        {
            $str .= '[' . $c['id'] . ",'" . $c['nom'] . "'],";
        }
        $str = substr($str,0,strlen($str)-1);
        $str .= ']';

        return $str;
    }
}