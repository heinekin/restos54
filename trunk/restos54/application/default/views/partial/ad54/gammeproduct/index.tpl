<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<blocs>
    <bloc type="page">
        <content method="innerHTML" id="main"><!--
            <h1>Vous pouvez ici gérer les gammes de produit existantes</h1>

<table id="tableMain">
                <tr>
                    <td valign="top" id="leftMain">
<div id="gammeproductgrid"></div>
<div id="gammeproductprint" style="display:none"></div>
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
                           {url: '/ad54/restgammeproduct',
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
            {name: "gamme", allowBlank: false},
            {name: "description", allowBlank: true}
            ]
        );

        // The new DataWriter component.
var writer = new Ext.data.JsonWriter({
            encode: true   // <-- don"t return encoded JSON -- causes Ext.Ajax#request to send data using jsonData config rather than HTTP params
        });
var store = new Ext.data.Store({
            id: "gamme",
            restful: true,     // <-- This Store is RESTful
            proxy: proxy,
            reader: reader,
            writer: writer

        });
var gammeColumns =  [
            {header: "gamme", width: 100, sortable: true, dataIndex: "gamme", editor: new Ext.form.TextField({})},
            {header: "description", width: 50, sortable: true, dataIndex: "description", editor: new Ext.form.TextField({})}
            ];

store.load();

var editor = new Ext.ux.grid.RowEditor({
            saveText: "Valider",
            cancelText: "Annuler"
        });

grid = new Ext.grid.GridPanel({
columnLines : true,
            iconCls: "icon-grid",
            frame: true,
            title: "Les gammes de produit",
            autoScroll: true,
            height: 300,
            stripeRows: true,
            store: store,
            plugins: [Ext.ux.grid.DataDrop,editor],
            columns : gammeColumns,

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

grid.render('gammeproductgrid');



/**
 * onAdd
 */
function onAdd(btn, ev) {
    var u = new store.recordType({
            id:"",
            gamme:"",
            description:""
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
columnLines : true,
            iconCls: "icon-grid",
            frame: true,
            title: "Les gammes de produit",
            autoScroll: true,
            height: 300,
            store: store,
            stripeRows: true,
            plugins: [Ext.ux.grid.DataDrop,editor],
            columns : gammeColumns,
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