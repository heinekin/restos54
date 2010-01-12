<?php
class Zend_View_Helper_ArrayJs
{
    function arrayJs($tab)
    {
        $tabStr = array();
        foreach($tab as $k => $v) {
            $tabStr[] = '["' . $k . '", "' . $v . '"]';
        }
        return '[' . implode(', ', $tabStr) . ']';
    }
}