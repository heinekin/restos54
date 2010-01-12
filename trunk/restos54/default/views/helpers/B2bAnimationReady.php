<?php
class Zend_View_Helper_B2bAnimationReady
{
    const READY_NOT_OBJECT = 1;
    const READY_WITH_OBJECT = 2;
    const NOT_READY_WITH_OBJECT = 3;

    function b2bAnimationReady($state, $idAnim)
    {
        $start_link = '<a onclick="MyDataGrid.action(\'setting\', {id: '.$idAnim.'});" title="setting" class="lien">';
        $end_link = '</a>';

        if($state === self::READY_NOT_OBJECT) {
            return '<img src="/images/icon/ok.png" alt="ready"/>';
        } elseif($state === self::READY_WITH_OBJECT) {
            return $start_link . '<img src="/images/icon/ok_setting.png" alt="ready"/></a>' . $end_link;
        } elseif($state === self::NOT_READY_WITH_OBJECT) {
            return $start_link . '<img src="/images/icon/warn_setting.png" alt="settings"/></a>' . $end_link;
        } else {
            return '';
        }
    }
}