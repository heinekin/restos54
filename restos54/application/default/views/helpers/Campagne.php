<?php
class Zend_View_Helper_Campagne
{
    function campagne()
    {
        $session = (Zend_Session::namespaceGet('Campagne'));
        $campagne = $session['camp'];
        $date = $campagne->get_year();
        
        return $date;
    }
}