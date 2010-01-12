<?php
class Zend_View_Helper_B2bAnimationSee extends Zend_View_Helper_Abstract
{
    const READY_NOT_OBJECT = 1;
    const READY_WITH_OBJECT = 2;
    const NOT_READY_WITH_OBJECT = 3;

    function b2bAnimationSee($state, $idAnim)
    {
        if($state === self::READY_NOT_OBJECT || $state === self::READY_WITH_OBJECT) {
            //return '<a onclick="MyDataGrid.action(\'see\', {id: '.$idAnim.'});" title="see" class="lien"><img src="/images/icon/loupe.png" alt="ready"/></a>';
            return '<a onclick="window.open(\'/' . $this->view->module() . '/' . $this->view->controller() . '/see/id/' . $idAnim . '\');" title="see" class="lien"><img src="/images/icon/loupe.png" alt="ready"/></a>';
        } else {
            return '';
        }
    }
}