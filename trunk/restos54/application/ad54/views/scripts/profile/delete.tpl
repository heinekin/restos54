<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<blocs>
    {if $id_profile > 0}
    <bloc type="script">
        <content><![CDATA[ $('spanProfile_'+{$id_profile}).up('div.x-grid3-row').remove(); ]]></content>
    </bloc>
    {/if}
</blocs>