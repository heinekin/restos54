<?php
class Zend_View_Helper_Mois
{

    function mois()
    {
        $session = (Zend_Session::namespaceGet('Campagne'));
        $campagne = $session['camp'];
        $id_campagne = $campagne->getSelected();
        $sql = "SELECT DISTINCT DATE_FORMAT(l.date, '%m/%Y') as date
                FROM livraison AS l
                WHERE l.id_campagne = ".$id_campagne."
                ORDER BY l.date ASC";

        $result = (array)Zend_Registry::get('db')->fetchAll($sql);
        $str='(';
        $i=1;
        foreach($result as $month)
        {
            $str .= " record.data.livraison".$i. " +";
            $i++;
        }
        $str = substr($str,0,strlen($str)-1);
        $str .= ')';
        return $str;
    }
   
}