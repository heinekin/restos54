<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<blocs>
    <bloc type="page">
        <content method="innerHTML" id="main"><!--
            <h1>Vous pouvez ici modifier la liste des produits</h1>
<table id="tableMain">
                <tr>
                    <td valign="top" id="leftMain">
<div id="grid-example"></div>
<div id="productprint" style="display:none"></div>
                    </td>
                    <td valign="top"><div id="rightMain" />
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
var reader = new Ext.data.JsonReader({
            totalProperty: "total",
            successProperty: "success",
            idProperty: "id",
            root: "data"
        }, [
            {name: "id"},
            {name: "reference", allowBlank: false, type: 'int'},
            {name: "nom", allowBlank: false},
            {name: "conditionnement", allowBlank: false, type: 'int'},
            {name: "portions", allowBlank: false, type: 'int'},
            {name: "nb_colis", allowBlank: false, type: 'int'},
            {name: "inventaire", allowBlank: false, type: 'int'}
            ]
        );

        // The new DataWriter component.
var writer = new Ext.data.JsonWriter({
            encode: true,
            writeAllFields: true
});
var store = new Ext.data.GroupingStore({
            id: "produits",
            restful: true,     // <-- This Store is RESTful
            proxy: proxy,
            reader: reader,
            writer: writer,
sortInfo:{field: 'id', direction: "ASC"},
groupField:''

        });
var productColumns =  [
{header: "id", sortable: true, dataIndex: "id"},
            {header: "reference", sortable: true, dataIndex: "reference"},
            {header: "nom", sortable: true, dataIndex: "nom"},
            {header: 'conditionnement', dataIndex: 'conditionnement', sortable: true},
            {header: 'portions', dataIndex: 'portions', sortable: true},
            {header: "nb de colis attribués", css: '{color:red;}', sortable: true, dataIndex: "nb_colis", editor: new Ext.form.NumberField({})},
            {header: "inventaire fin campagne précedente", sortable: true, dataIndex: "inventaire"}
];

store.load();

var editor = new Ext.ux.grid.RowEditor({
            saveText: "Valider",
            cancelText: "Annuler"
        });

grid = new Ext.grid.GridPanel({

            iconCls: "icon-grid",
            frame: true,
            title: "Les prévisions",
            autoScroll: true,
            height: 500,
            store: store,
            stripeRows: true,
            plugins: [editor],
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