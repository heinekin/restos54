<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<blocs>
    <bloc type="page">
        <content method="innerHTML" id="main"><!--
            <h1>Créez un nouveau bon de sortie vers le centre de {$centre} - Semaine {$semaine}</h1>
<table id="tableMain">
                <tr>
                    <td valign="top" id="leftMain">
<div id="bds"></div>
                    </td>
                    <td valign="top"><div id="rightMain"><div id="livraisonsPanel"></div></div>
                    </td>
                </tr>
        </table>
        --></content>

    </bloc>
<bloc type="script">        <content><![CDATA[ {literal}


    // turn on validation errors beside the field globally
    Ext.form.Field.prototype.msgTarget = 'side';
var test = {/literal}{$bds}{literal};

Ext.MessageBox.buttonText.yes = "Oui";
Ext.MessageBox.buttonText.no = "Non";

if(test)
{
    Ext.Msg.show({
       title:'Un bon de sortie existe déjà pour le centre de {/literal}{$centre}{literal} à la semaine {/literal}{$semaine}{literal}',
       msg: 'Voulez-vous créer un autre bon de sortie pour cette même semaine ? (Sinon serez redirigé vers le suivi des bons de sortie de ce centre)',
       buttons: Ext.Msg.YESNO,
       fn: processResult,
       icon: Ext.MessageBox.QUESTION,
        minWidth : 500
    });
}

function processResult(btn)
{
    if(btn == 'no'){
        redirect();
    }
}

var tab = new Array();
tab['P'] = 0;
tab['A'] = 0;
tab['D'] = 0;
tab['L'] = 0;
tab['C'] = 0;
tab['CE'] = 0;


var store = new Ext.data.JsonStore({
        autoDestroy: true,
        url: 'centres/bds/loadbds',
        storeId: 'myStore',
        root: 'data',
listeners: {
	update : function(store, record, operation ) {
            var myGrid = Ext.getCmp('bdsgrid');
            var allRecords = myGrid.store.getRange(0);
            var gamme = record.data.gamme;
            var type = record.data.type;

            if(gamme == 'P')
            {
                tab['P'] = 0;
                for (i=0;i < allRecords.length;i++)
                {
                    if(allRecords[i].data.gamme == "P" || allRecords[i].data.gamme == "M")
                    {
                       if(allRecords[i].data.nb_colis != ""){
                            tab['P'] += allRecords[i].data.nb_colis;
                        }
                        if(allRecords[i].data.collecte != ""){
                            tab['P'] += allRecords[i].data.collecte;
                        }

                    }
                }
                 Ext.get('total_p').frame("#E49C4F", 1, { duration: 1 });
            }
            else if(gamme == 'A')
            {
                tab['A'] = 0;
                for (i=0;i < allRecords.length;i++)
                {
                    if(allRecords[i].data.gamme == "A" || allRecords[i].data.type.include('ACCOMPAGN') || allRecords[i].data.gamme == "M")
                    {
                       if(allRecords[i].data.nb_colis != ""){
                            tab['A'] += allRecords[i].data.nb_colis;
                        }
                        if(allRecords[i].data.collecte!= ""){
                            tab['A'] += allRecords[i].data.collecte;
                        }

                    }
                }
                 Ext.get('total_a').frame("#E49C4F", 1, { duration:1, width:3 });
            }
            else if(gamme == 'D')
            {
                tab['D'] = 0;
                for (i=0;i < allRecords.length;i++)
                {
                    if(allRecords[i].data.gamme == "D")
                    {
                       if(allRecords[i].data.nb_colis != ""){
                            tab['D'] += allRecords[i].data.nb_colis;
                        }
                        if(allRecords[i].data.collecte!= ""){
                            tab['D'] += allRecords[i].data.collecte;
                        }

                    }
                }
                 Ext.get('total_d').frame("#E49C4F", 1, { duration: 1 });
            }
            else if(gamme == 'L')
            {
                tab['L'] = 0;
                for (i=0;i < allRecords.length;i++)
                {
                    if(allRecords[i].data.gamme == "L")
                    {
                       if(allRecords[i].data.nb_colis != ""){
                            tab['L'] += allRecords[i].data.nb_colis;
                        }
                        if(allRecords[i].data.collecte!= ""){
                            tab['L'] += allRecords[i].data.collecte;
                        }

                    }
                }
                 Ext.get('total_l').frame("#E49C4F", 1, { duration: 1 });
            }
            else if(gamme == 'M')
            {
                tab['M'] = 0;
                tab['A'] = 0;
                tab['P'] = 0;
                for (i=0;i < allRecords.length;i++)
                {
                    if(allRecords[i].data.gamme == "M")
                    {
                       if(allRecords[i].data.nb_colis != ""){
                            tab['M'] += allRecords[i].data.nb_colis;
                        }
                        if(allRecords[i].data.collecte!= ""){
                            tab['M'] += allRecords[i].data.collecte;
                        }

                    }

                    if(allRecords[i].data.gamme == "P")
                    {
                       if(allRecords[i].data.nb_colis != ""){
                            tab['P'] += allRecords[i].data.nb_colis;
                        }
                        if(allRecords[i].data.collecte!= ""){
                            tab['P'] += allRecords[i].data.collecte;
                        }

                    }
                    if(allRecords[i].data.gamme == "A" || allRecords[i].data.type.include('ACCOMPAGN'))
                    {
                       if(allRecords[i].data.nb_colis != ""){
                            tab['A'] += allRecords[i].data.nb_colis;
                        }
                        if(allRecords[i].data.collecte!= ""){
                            tab['A'] += allRecords[i].data.collecte;
                        }

                    }

                }
                tab['P'] += tab['M'];
                tab['A'] += tab['M'];
                Ext.get('total_p').frame("#E49C4F", 1, { duration: 1 });
                Ext.get('total_a').frame("#E49C4F", 1, { duration:1, width:3 });
            }
            else if(record.data.type.include('ACCOMPAGN'))
            {
                tab['A'] = 0;
                for (i=0;i < allRecords.length;i++)
                {
                    if(allRecords[i].data.gamme == "A" || allRecords[i].data.type.include('ACCOMPAGN') || allRecords[i].data.gamme == "M")
                    {
                       if(allRecords[i].data.nb_colis != ""){
                            tab['A'] += allRecords[i].data.nb_colis;
                        }
                        if(allRecords[i].data.collecte!= ""){
                            tab['A'] += allRecords[i].data.collecte;
                        }

                    }
                }
                 Ext.get('total_a').frame("#E49C4F", 1, { duration:1, width:3 });
            }

            if(record.data.type.include('CONGELES'))
            {
                tab['C'] = 0;
                for (i=0;i < allRecords.length;i++)
                {
                    if(allRecords[i].data.type.include('CONGELES'))
                    {
                       if(allRecords[i].data.nb_colis != ""){
                            tab['C'] += allRecords[i].data.nb_colis;
                        }
                        if(allRecords[i].data.collecte!= ""){
                            tab['C'] += allRecords[i].data.collecte;
                        }

                    }
                }
                 Ext.get('total_c').frame("#E49C4F", 1, { duration: 1 });
            }
            if(record.data.type.include(' CE '))
            {
                tab['CE'] = 0;
                for (i=0;i < allRecords.length;i++)
                {
                    if(allRecords[i].data.type.include(' CE '))
                    {
                       if(allRecords[i].data.nb_colis != ""){
                            tab['CE'] += allRecords[i].data.nb_colis;
                        }
                        if(allRecords[i].data.collecte!= ""){
                            tab['CE'] += allRecords[i].data.collecte;
                        }

                    }
                }
                 Ext.get('total_ce').frame("#E49C4F", 1, { duration: 1 });
            }

           Ext.getCmp("company-form").getForm().setValues({
                                Total_protides: tab['P'],
                                total_acc: tab['A'],
                                total_cong: tab['C'],
                                total_ce: tab['CE'],
                                total_dessert : tab['D'],
                                total_laitage : tab['L']
                                                          });

        }
},
        fields: ['id', {name: 'reference', type:'int'}, 'nom', 'boitage', {name: 'poids', type:'float'}, {name: 'conditionnement', type:'int'}, 'type',
                 {name: 'nb_colis', type:'int'}, {name: 'collecte', type:'int'}, {name: 'portions', type:'int'}, {name: 'nb_colis_total', type:'int'}, 'gamme', {name: 'poids_total', type:'int'}]
    });

    // manually load local data
    store.load();

var colModel = new Ext.grid.ColumnModel([
        {header: "id",  sortable: true, dataIndex: 'id', hidden: true},
        {header: "ref.", width: 50, sortable: true, dataIndex: "reference",tooltip:'Reference du produit'},
        {header: "produit", width: 80, sortable: true, dataIndex: "nom",tooltip:'Nom du produit'},
        {header: "boitage", width: 50, sortable: true, dataIndex: "boitage",tooltip:'Boitage du produit'},
        {header: "poids", width: 40, sortable: true, dataIndex: "poids",tooltip:'Poids du produit'},
        {header: "conditionnement", width: 40, sortable: true, dataIndex: "conditionnement",tooltip:'Conditionnement du produit'},
        {header: "type", width: 70, sortable: true, dataIndex: "type",tooltip:'Type de produit'},
        {header: "portions", width: 50, sortable: true, dataIndex: "portions",tooltip:'nb. de portions du produit'},
        {header: "gamme", width: 50, sortable: true, dataIndex: "gamme",tooltip:'Gamme du produit'},
        {css:"background-color:#FFCCCC;font-weight:bold;", header: "qté à livrer en colis",tooltip:'Quantité à livrer en colis, </br>hors collecte', width: 80, sortable: true, dataIndex: "nb_colis", editor: new Ext.form.NumberField({})},
        {header: "collecte", css:"background-color:#FFCCCC;font-weight:bold;", width: 50, sortable: true, dataIndex: "collecte", editor: new Ext.form.NumberField({}),tooltip:'Quantité à livrer en colis,</br> provenant de la collecte'},
        {header: "total portions livrées", css:"background-color:#FFFF99; color:red; font-weight:bold;", width: 50,tooltip:'Total des portions à livrer', sortable: true, dataIndex: "nb_colis_total",
         renderer: function(v, params, record){

                return (record.data.conditionnement * record.data.portions*( record.data.nb_colis + record.data.collecte ));
                            } },
        {header: "poids à livrer hors collecte", css:"background-color:#FFFF99; color:blue; font-weight:bold;",width: 50,tooltip:'Poids à livrer hors collecte', sortable: true, dataIndex: "poids_total",
         renderer: function(v, params, record){

                return Math.round((record.data.poids * record.data.conditionnement * record.data.nb_colis)*100)/100;
                            } }
    ]);
Ext.MessageBox.buttonText.yes = "OK";
var editor = new Ext.ux.grid.RowEditor({
            saveText: "Valider",
            cancelText: "Annuler"
        });
    var gridForm = new Ext.FormPanel({
        id: 'company-form',
        title: 'Bon de sortie - Centre {/literal}{$centre}{literal} - Semaine {/literal}{$semaine}{literal}',
        buttons: [{
            id: 'save',
            text: 'ENVOYER',
            width: 200,
            iconCls: "silk-disk",
            height: 40,
            handler: function(){

       var storeValue=[];
       var myGrid = Ext.getCmp('bdsgrid');
       var allRecords = myGrid.store.getRange(0);
       var nb_colis = 0;
       var collecte = 0;
       // je mets les données dans une variable storeValue
       for (i=0;i < allRecords.length;i++)
       {
         storeValue[i] = allRecords[i].data;
         if(allRecords[i].data.nb_colis != "")
            nb_colis = 1;
         if(allRecords[i].data.collecte != "")
            collecte = 1;
       }
       // je récup les données de la form
       var dataForm = gridForm.getForm().getValues();
       var valid = gridForm.getForm().isValid();
       if((nb_colis == 1 || collecte == 1 ) && valid == true)
       {
           // j'envoi la requete ajax avec les données complètes.
           Ext.Ajax.request({

             url: 'centres/bds/savebds',

             // je paramètre la requête
             params: {
                date: dataForm.date,
                total_ce: dataForm.total_ce,
                grid1: Ext.encode(storeValue)  // Données de la grid au format JSON...
             },

             callback: function(options,success,response){

         Ext.MessageBox.alert('Le bon de sortie a bien été créé !', 'Vous allez être dirigé vers le suivi des bons de sortie de {/literal}{$centre}{literal}', redirect);

             }

           });
       }
       else
       {
            if(!valid)
                Ext.MessageBox.alert('Erreur', 'La date saisie est erronée !');
            else
                Ext.MessageBox.alert('Erreur', 'Le bon de sortie ne contient aucune données !');
       }
}

        }],
        frame: true,
        labelAlign: 'left',
        bodyStyle:'padding:5px',
        width: 900,
        //layout: 'fit',
        //autoFit: true,

        tbar: [{
            text: "Ouvrir dans une fenêtre indépendante",
            iconCls: "silk-application-go",
            handler: onOpen
        }],
        layout: 'column',    // Specifies that the items will now be arranged in columns
        items: [{
            columnWidth: 0.7,
            items: {
                xtype: 'editorgrid',
                layout:'fit',
                stripeRows: true,
                columnLine: true,
autoScroll:true,
                id: 'bdsgrid',
                ds: store,
                cm: colModel,
                sm: new Ext.grid.RowSelectionModel({
                    singleSelect: true
                }),
                height: 350,
                border: true,
                plugins: [editor],
                listeners: {
                    viewready: function(g) {
                        g.getSelectionModel().selectRow(0);
                    } // Allow rows to be rendered.
                },
                view: new Ext.grid.GridView({
                    sortAscText: 'Tri Ascendant',
                    sortDescText: 'Tri Descendant',
                    columnsText: 'Colonnes'
                    })
            }
        },{
            columnWidth: 0.30,
            //autoFit: true,
            xtype: 'fieldset',
            labelWidth: 120,
            frame:true,
            title:'Détails',
            defaults: {width: 100, border:true},    // Default config options for child items
            defaultType: 'textfield',
            autoHeight: true,
            bodyStyle: Ext.isIE ? 'padding:0 0 5px 15px;' : 'padding:10px 15px;',
            border: true,
            style: {
                "margin-left": "10px", // when you add custom margin in IE 6...
                "margin-right": Ext.isIE6 ? (Ext.isStrict ? "-10px" : "-13px") : "0"  // you have to adjust for it somewhere else
            },
            items: [
              {
                xtype: 'textfield',
                readOnly: true,
                id: 'total_p',
                fieldLabel: 'Total protides',
                name: 'Total_protides'
            },{
                xtype: 'textfield',
                readOnly: true,
                id: 'total_a',
                fieldLabel: 'Total accompagnement',
                name: 'total_acc'
            },{
                xtype: 'textfield',
                readOnly: true,
                id: 'total_c',
                fieldLabel: 'Total congelé',
                name: 'total_cong'
            },{
                xtype: 'textfield',
                readOnly: true,
                id: 'total_ce',
                fieldLabel: 'Total CE',
                name: 'total_ce'
            },{
                xtype: 'textfield',
                readOnly: true,
                id: 'total_d',
                fieldLabel: 'Total Dessert',
                name: 'total_dessert'
            },{
                xtype: 'textfield',
                readOnly: true,
                id: 'total_l',
                fieldLabel: 'Total Laitage',
                name: 'total_laitage'
            },{
                xtype: 'datefield',
                fieldLabel: 'Date départ entrepôt',
                name: 'date',
                format : 'd/m/Y',
                allowBlank: false,
                minValue: '{/literal}{$this->campagne()}{literal}',
                minText: 'La date doit être supérieur ou égale à la date de lancement de la campagne le {0}',
                blankText: 'Vous devez renseigner la date de départ'
            }]
        }],
        renderTo: 'bds'
    });


function redirect()
{
    var loading = new Loading(1);


    new Ajax.Request('centres/bds/suivi',
        {
            method:'get',
            onCreate: function(req) {
                loading.start();
            },
            onSuccess: function(req) {
                loading.stop();
                xmlResponse(req.responseXML);
            }

        });
}
function onOpen()
{
//gridForm.destroy();
    var window = new Ext.Window({
            width: 1100,
            autoScroll: true,
            height:550,
            minWidth: 300,
            minHeight: 400,
            layout: 'fit',
            plain:true,
            bodyStyle:'padding:5px;',
            buttonAlign:'center',
            items:[gridForm],
            maximizable: true,
            collapsible: true
        });
    window.show();
}


        {/literal}]]></content>    </bloc>
</blocs>