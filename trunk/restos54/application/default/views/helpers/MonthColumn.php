<?php
class Zend_View_Helper_Monthcolumn
{

    function monthcolumn()
    {
        $session = (Zend_Session::namespaceGet('Campagne'));
        $campagne = $session['camp'];
        $id_campagne = $campagne->getSelected();
        $sql = "SELECT DISTINCT DATE_FORMAT(l.date, '%m/%Y') as date
                FROM livraison AS l
                WHERE l.id_campagne = ".$id_campagne."
                ORDER BY l.date ASC";

        $result = (array)Zend_Registry::get('db')->fetchAll($sql);

        $str='';
        $i=0;
        foreach($result as $month)
        {
            $i++;
            $str .= '{id:\'livraison'.$i.'\',header: \'Livraisons '.$month['date'] . '\',sortable: true, dataIndex: \'livraison'.$i.'\'},';
        }

        return $str;
    }
   
}