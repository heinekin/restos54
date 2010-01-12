<?php
class Zend_View_Helper_Profile
{
    function profile()
    {
        $profile = new Profile();
        $result = $profile->fetchAll();
        $tab = $result->toArray();
        $str = '[';
        foreach($tab as $c)
        {
            $str .= '[' . $c['id'] . ",'" . $c['code'] . "'],";
        }
        $str = substr($str,0,strlen($str)-1);
        $str .= ']';

        return $str;
    }
}