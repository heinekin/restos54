<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<blocs>
    {if $flagError == 'true'}
        {if $errorMessage != ''}
        <bloc type="script">
            <content><![CDATA[
                console.warn('{$errorMessage}');
            ]]></content>
        </bloc>
        {/if}
    {else}
    <bloc type="script">
        <content><![CDATA[
            FeatureList.deleteEntry({$idnode});
        ]]></content>
    </bloc>
    {/if}
</blocs>