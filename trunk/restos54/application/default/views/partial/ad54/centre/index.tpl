<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<blocs>
    <bloc type="page">
        <content method="innerHTML" id="main"><!--
            <h1>Vous pouvez ici modifier la liste des centres</h1>
<table id="tableMain">
                <tr>
                    <td valign="top" id="leftMain">
<div id="grid-example"></div>
<div id="centreprint" style="display:none"></div>
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
                           {url: '/ad54/restcentre',
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
            {name: "nom", allowBlank: false},
            {name: "type", allowBlank: false}
            ]
        );

        // The new DataWriter component.
var writer = new Ext.data.JsonWriter({
            encode: true   // <-- don"t return encoded JSON -- causes Ext.Ajax#request to send data using jsonData config rather than HTTP params
        });
var store = new Ext.data.GroupingStore({
            id: "centre",
            restful: true,     // <-- This Store is RESTful
            proxy: proxy,
            reader: reader,
            writer: writer,
sortInfo:{field: 'type', direction: "ASC"},
groupField:''

        });
var centreColumns =  [
            {header: "id", width: 40, sortable: true, dataIndex: "id"},
            {header: "nom", width: 100, sortable: true, dataIndex: "nom", editor: new Ext.form.TextField({})},
            {header: "type", width: 50, sortable: true, dataIndex: "type", editor: new Ext.form.TextField({})}
            ];

store.load();

var editor = new Ext.ux.grid.RowEditor({
            saveText: "Valider",
            cancelText: "Annuler"
        });

grid = new Ext.grid.GridPanel({

            iconCls: "icon-grid",
            frame: true,
collapsible: true,
            title: "Centres",
            autoScroll: true,
            height: 300,
            store: store,
            stripeRows: true,
            plugins: [Ext.ux.grid.DataDrop,editor],
            columns : centreColumns,

            tbar: [{
                text: "Ajouter",
                iconCls: "silk-add",
                handler: onAdd
            }, "-", {
                text: "Supprimer",
                iconCls: "silk-delete",
                handler: onDelete
            }, "-", {
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
groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Items" : "Item"]})'
})
        });

grid.render('grid-example');



/**
 * onAdd
 */
function onAdd(btn, ev) {
    var u = new store.recordType({
            id:"",
            nom:"",
            type:""
    });
    editor.stopEditing();
    grid.store.insert(0, u);
    editor.startEditing(0);
}
/**
 * onDelete
 */
function onDelete() {
    var rec = grid.getSelectionModel().getSelected();
    if (!rec) {
        return false;
    }
    grid.store.remove(rec);
}
function onOpen() {
grid.destroy();
grid = new Ext.grid.GridPanel({
            iconCls: "icon-grid",
            frame: true,
            title: "Centres",
collapsible: true,
            autoScroll: true,
            height: 300,
            store: store,
            stripeRows: true,
            plugins: [Ext.ux.grid.DataDrop,editor],
            columns : centreColumns,
            tbar: [{
                text: "Ajouter",
                iconCls: "silk-add",
                handler: onAdd
            }, "-", {
                text: "Supprimer",
                iconCls: "silk-delete",
                handler: onDelete
            }, "-", {
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
            viewConfig: {
                forceFit: true,
                sortAscText: 'Tri Ascendant',
                sortDescText: 'Tri Descendant',
                columnsText: 'Colonnes'
            }
        });
var window = new Ext.Window({
        title: 'Fenêtre volante',
        width: 750,
        height:400,
        minWidth: 300,
        minHeight: 400,
        layout: 'fit',
        plain:true,
        bodyStyle:'padding:5px;',
        buttonAlign:'center',
        items: grid
    });
window.show();
}

function onPrint() {

Ext.ux.Printer.print(grid);
}
        {/literal}]]></content>    </bloc>
</blocs>