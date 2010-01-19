<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<blocs>
    <bloc type="page">
        <content method="innerHTML" id="main"><!--
            <h1> Prévisions et Répartitions pour l'ensemble de la campagne</h1>
<h2>Vous pouvez modifier le nombre de colis attribués attribué à chaque produit pour la campagne</h2>
<table id="tableMain">
                <tr>
                    <td valign="top" id="leftMain">
<div id="grid-example"></div>
                    </td>
                    <td valign="top"><div id="rightMain"></div>
                    </td>
                </tr>
        </table>
        --></content>

    </bloc>
<bloc type="script">        <content><![CDATA[ {literal}

var grid = null;
var app = new Ext.App({});
var proxy = new Ext.data.HttpProxy(
                           {url: '/ad54/restqprev',
                            listeners : {
                                write : function(store, action, result, response, rs) {
                                        app.setAlert(response.success, response.message);
                                        if(response.success != "Erreur !"){
                                        rs.commit();grid.store.load();}
                }
                           }});

var MyRecord = Ext.ux.data.CalcRecord.create([
            {name: "id"},
            {name: "reference", allowBlank: false, type: 'int'},
            {name: "nom", allowBlank: false},
            {name: "conditionnement", allowBlank: false, type: 'int'},
            {name: "portions", allowBlank: false, type: 'int'},
            {name: "nb_colis", allowBlank: false, type: 'int'},
            {name: "inventaire", allowBlank: false, type: 'int'},
            {name: 'sum', dependencies: ['inventaire', 'nb_colis'], notDirty: true, calc: function(record) {
            
		return (record.get('inventaire') + record.get('nb_colis')) * record.get('conditionnement');
	}}])

var reader = new Ext.data.JsonReader({
            totalProperty: "total",
            successProperty: "success",
            idProperty: "id",
            root: "data"
        },MyRecord);

        // The new DataWriter component.
var writer = new Ext.data.JsonWriter({
            encode: true,
            writeAllFields: true
});
var store = new Ext.data.GroupingStore({
            id: "previsions",
            restful: true,     // <-- This Store is RESTful
            proxy: proxy,
            reader: reader,
            writer: writer,
sortInfo:{field: 'id', direction: "ASC"},
groupField:''

        });

  function pctChange(val){

            return '<span style="color:red;">' + val + '</span>';
      
    }

var productColumns =  [
            {header: "id", sortable: true, dataIndex: "id", hidden: true},
            {header: "reference", sortable: true, dataIndex: "reference"},
            {header: "nom", sortable: true, dataIndex: "nom"},
            {header: 'conditionnement', dataIndex: 'conditionnement', sortable: true},
            {header: 'portions', dataIndex: 'portions', sortable: true},
            {header: "nb de colis attribués", css: '{color:red;}', sortable: true, dataIndex: "nb_colis", editor: new Ext.form.NumberField({})},
            {header: "inventaire fin campagne précedente", sortable: true, dataIndex: "inventaire"},
            {header: 'Total Boites ou Pack', dataIndex: 'sum', sortable: true}

];

store.load();

var cellTips = new Ext.ux.plugins.grid.CellToolTips([
    	{ field: 'sum', tpl: '<b>Formule:</b><br />(inventaire + nb_colis) * conditionnement<br />' }
    ]);

var editor = new Ext.ux.grid.RowEditor({
            saveText: "Valider",
            cancelText: "Annuler"
        });

grid = new Ext.grid.GridPanel({
columnLines : true,
            iconCls: "icon-grid",
            frame: true,
            title: "Les prévisions",
            autoScroll: true,
            height: 500,
            store: store,
            stripeRows: true,
            plugins: [editor,cellTips],
            columns : productColumns,

            tbar: [{
                text: "Imprimer",
                iconCls: "silk-printer",
                handler: onPrint
            }, "-", {
                text: "Ouvrir dans une fenêtre indépendante",
                iconCls: "silk-application-go",
                handler: onOpen
            }, "-", {
                id: 'grid-excel-button',
                text: 'Ouvrir avec Excel',
                iconCls: "silk-page-excel",
                handler: function(){
                        document.location='data:application/vnd.ms-excel;base64,' + Base64.encode(grid.getExcelXml());
                }
    				}],view: new Ext.grid.GroupingView({
forceFit:true,
groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "elements" : "element"]})',
sortAscText: 'Tri Ascendant',
sortDescText: 'Tri Descendant',
columnsText: 'Colonnes',
groupByText: 'Grouper en fonction de ce champ',
showGroupsText: 'Activer/Desactiver Groupe'
})
        });

grid.render('grid-example');


function onOpen() {
grid.hide();
var grid2 = new Ext.grid.GridPanel({
columnLines : true,
            iconCls: "icon-grid",
            autoScroll: true,
            height: 300,
            store: store,
            stripeRows: true,
            plugins: [Ext.ux.grid.DataDrop,editor],
            columns : productColumns,
            tbar: [{
                text: "Imprimer",
                iconCls: "silk-printer",
                handler: onPrint
            }, "-", {
                text: "Ouvrir dans une fenêtre indépendante",
                iconCls: "silk-application-go",
                handler: onOpen
            }, "-", {
                id: 'grid-excel-button',
                text: 'Ouvrir avec Excel',
                iconCls: "silk-page-excel",
                handler: function(){
                        document.location='data:application/vnd.ms-excel;base64,' + Base64.encode(grid.getExcelXml());
                }
    				}],
            view: new Ext.grid.GroupingView({
            forceFit:true,
            groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "elements" : "element"]})',
sortAscText: 'Tri Ascendant',
sortDescText: 'Tri Descendant',
columnsText: 'Colonnes',
groupByText: 'Grouper en fonction de ce champ',
showGroupsText: 'Activer/Desactiver Groupe'
            })
        });
var window = new Ext.Window({
        title: 'Les prévisions',
        width: 750,
        height:400,
        minWidth: 300,
        minHeight: 400,
        layout: 'fit',
        plain:true,
        bodyStyle:'padding:5px;',
        buttonAlign:'center',
        items: grid2,
        maximizable: true,
        collapsible: true
    });
window.show();
}

function onPrint() {

Ext.ux.Printer.print(grid);
}



        {/literal}]]></content>    </bloc>
</blocs>