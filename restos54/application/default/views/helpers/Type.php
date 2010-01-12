<?php
class Zend_View_Helper_Type
{
    function type()
    {
        $type = new ProductType();
        $result = $type->fetchAll();
        $tab = $result->toArray();
        $str = '[';
        foreach($tab as $c)
        {
            $str .= '[' . $c['id'] . ",'" . $c['type'] . "'],";
        }
        $str = substr($str,0,strlen($str)-1);
        $str .= ']';
        return $str;
    }
}