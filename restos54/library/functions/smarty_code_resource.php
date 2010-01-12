<?php
// put these function somewhere in your application
function code_get_template ($tpl_code, &$tpl_source, &$smarty_obj)
{
    // do database call here to fetch your template,
    // populating $tpl_source
    $templates = new B2b_Template();
    $row = $templates->fetchRow("code LIKE '" . $tpl_code . "'");
    if(!is_null($row)) {
        $tpl_source = $row->content;
        return true;
    } else {
        return false;
    }
}

function code_get_timestamp($tpl_code, &$tpl_timestamp, &$smarty_obj)
{
    $templates = new B2b_Template();
    $row = $templates->fetchRow("code LIKE '" . $tpl_code . "'");
    if(!is_null($row)) {
        $tpl_timestamp = strtotime($row->modification_date);
        return true;
    } else {
        return false;
    }
}

function code_get_secure($tpl_code, &$smarty_obj)
{
    // assume all templates are secure
    return true;
}

function code_get_trusted($tpl_code, &$smarty_obj)
{
    // not used for templates
}
?>