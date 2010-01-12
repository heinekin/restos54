<?php
// put these function somewhere in your application
function id_get_template ($tpl_id, &$tpl_source, &$smarty_obj)
{
    // do database call here to fetch your template,
    // populating $tpl_source
    $templates = new B2b_Template();
    $row = $templates->fetchRow("id  = " . $tpl_id);
    if(!is_null($row)) {
        $tpl_source = $row->content;
        return true;
    } else {
        return false;
    }
}

function id_get_timestamp($tpl_id, &$tpl_timestamp, &$smarty_obj)
{
    $templates = new B2b_Template();
    $row = $templates->fetchRow("id = " . $tpl_id);
    if(!is_null($row)) {
        $tpl_timestamp = strtotime($row->modification_date);
        return true;
    } else {
        return false;
    }
}

function id_get_secure($tpl_id, &$smarty_obj)
{
    // assume all templates are secure
    return true;
}

function id_get_trusted($tpl_id, &$smarty_obj)
{
    // not used for templates
}
?>