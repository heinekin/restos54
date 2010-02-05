<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<blocs>
    <bloc type="page">
        <content method="innerHTML" id="rightMain"><![CDATA[
        <h2>Nouveau profile</h2>
        {$formulaire}
        ]]></content>
    </bloc>
    {if $flagError == 'true'}
    <bloc type="script">
        <content><![CDATA[
            {section name=idx loop=$errorFields}
                $('{$errorFields[idx]}').addClassName('chpError');
            {/section}
        ]]></content>
    </bloc>
    {/if}
</blocs>