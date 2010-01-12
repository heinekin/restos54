<?php
class Zend_View_Helper_Semaines
{
    function semaines()
    {
        $session = (Zend_Session::namespaceGet('Campagne'));
        $campagne = $session['camp'];
        $id_semaine = $campagne->getSemaine();

       /* $semaine = new Semaine();
        $result = $semaine->fetchRow('id_campagne='.$id_campagne);*/

   /* $str = '[';
        for($i=0; $i<$result->semaine; $i++)
        {
            $str .= "{title: 'Semaine" . ($i+1) . "',autoload: loadGrid(".$i."),scripts: true},";
        }
        $str = substr($str,0,strlen($str)-1);
        $str .= ']';*/

        return $id_semaine;;
    }
}