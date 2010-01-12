<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<blocs>
    <bloc type="page">
        <content method="innerHTML" id="main"><!--
            <h1>C'est ici que vous pouvez créer une nouvelle campagne</h1>
<table id="tableMain">
                <tr>
                    <td valign="top" id="leftMain">
<div id="campagne"></div>
                    </td>
                    <td valign="top"><div id="rightMain" />
                    </td>
                </tr>
        </table>


        --></content>

    </bloc>
<bloc type="script">        <content><![CDATA[ {literal}

// create the combo instance
var combo = new Ext.form.ComboBox({
fieldLabel: 'Type de campagne',
    typeAhead: true,
    triggerAction: 'all',
    lazyRender:true,
    mode: 'local',
    store: new Ext.data.ArrayStore({
        id: 0,
        fields: [
            'id',
            'type'
        ],
        data:[[0, 'Campagne Hiver'], [1,'Campagne été']]
    }),
    valueField: 'id',
    displayField: 'type',
    hiddenName: 'typeID',
    allowBlank:false


});

var simple = new Ext.FormPanel({
        labelWidth: 75, // label settings here cascade unless overridden
        url:'ad54/Campagne/new',
        frame:true,
        title: 'Nouvelle campagne',
        bodyStyle:'padding:5px 5px 0',
        width: 350,
        defaults: {width: 230},
        defaultType: 'textfield',

        items: [combo,
              new Ext.form.DateField({
                fieldLabel: 'Date',
                name: 'time',
                allowBlank:false
            })
        ],

        buttons: [{
            text: 'Valider',
            handler: submit

        }]
});

function submit(){
    simple.getForm().submit({
        success:onSuccess
        ,failure:onFailure
        ,params:{cmd:'save'}
        ,waitMsg:'Chargement...'
    });
}

 function onSuccess(form, action) {
 Ext.Msg.show({
 title:'Réussite'
 ,msg:'La campagne à été créée'
 ,modal:true
 ,icon:Ext.Msg.INFO
 ,buttons:Ext.Msg.OK
 });
location.reload() ; 
 } // eo function onSuccess

function onFailure(form, action) {
 Ext.Msg.show({
 title:'Echec'
 ,msg:action.result.error
 ,modal:true
 ,icon:Ext.Msg.ERROR
 ,buttons:Ext.Msg.OK
 });
 } // eo function onFailure



    simple.render('campagne');













        {/literal}]]></content>    </bloc>
</blocs>