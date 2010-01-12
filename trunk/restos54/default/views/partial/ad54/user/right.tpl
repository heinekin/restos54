<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<blocs>
    <bloc type="page">
        <content method="innerHTML" id="rightMain"><![CDATA[
            <div id="openPanel">{$formulaire}</div>
        ]]></content>
    </bloc>
<bloc type="script">        <content><![CDATA[ {literal}

var window = new Ext.Window({
            title: 'Les droits de l\'utilisateur ' + record_user.data.Prenom + ' ' + record_user.data.Nom,
            width: 350,
            height:400,
            minWidth: 350,
            minHeight: 400,
            layout: 'fit',
            plain:true,
            bodyStyle:'padding:5px;',
            buttonAlign:'center',
            maximizable: true,
            autoScroll: true,
            collapsible: true,
            contentEl: 'openPanel'
        });
        window.show();
     console.log(Ext.get('grid1'));

        {/literal}]]></content>    </bloc>
</blocs>