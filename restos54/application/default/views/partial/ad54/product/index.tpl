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
// create reusable renderer
Ext.util.Format.comboRenderer = function(combo){
    return function(value){
        var record = combo.findRecord(combo.valueField, value);
        return record ? record.get(combo.displayField) : combo.valueNotFoundText;
    }
}

// create the combo instance
var combo_gamme = new Ext.form.ComboBox({
    typeAhead: true,
    triggerAction: 'all',
    lazyRender:true,
    mode: 'local',
    store: new Ext.data.ArrayStore({
        id: 0,
        fields: [
            'id',
            'gamme'
        ],
        data: {/literal}{$this->gamme()}{literal}
    }),
    valueField: 'id',
    displayField: 'gamme',
    hiddenName: 'gammeID'


});

var combo_type = new Ext.form.ComboBox({
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
        data: {/literal}{$this->type()}{literal}
    }),
    valueField: 'id',
    displayField: 'type',
    hiddenName: 'typeID'


});


var grid = null;
var app = new Ext.App({});
var proxy = new Ext.data.HttpProxy(
                           {url: '/ad54/restproduct',
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
            {name: "reference", allowBlank: false},
            {name: "nom", allowBlank: false},
            {name: "conditionnement", allowBlank: false},
            {name: "poids", allowBlank: false},
            {name: "boitage", allowBlank: false},
            {name: "portions", allowBlank: false},
            {name: "id_gamme", allowBlank: false},
            {name: "id_type", allowBlank: false}
            ]
        );

        // The new DataWriter component.
var writer = new Ext.data.JsonWriter({
            encode: true   // <-- don"t return encoded JSON -- causes Ext.Ajax#request to send data using jsonData config rather than HTTP params
        });
var store = new Ext.data.GroupingStore({
            id: "produits",
            restful: true,     // <-- This Store is RESTful
            proxy: proxy,
            reader: reader,
            writer: writer,
sortInfo:{field: 'id_type', direction: "ASC"},
groupField:''

        });
var productColumns =  [
            {header: "reference", width: 50, sortable: true, dataIndex: "reference", editor: new Ext.form.TextField({})},
            {header: "nom", width: 50, sortable: true, dataIndex: "nom", editor: new Ext.form.TextField({})},
            {header: "conditionnement", width: 50, sortable: true, dataIndex: "conditionnement", editor: new Ext.form.TextField({})},
            {header: "poids", width: 50, sortable: true, dataIndex: "poids", editor: new Ext.form.TextField({})},
            {header: "boitage", width: 50, sortable: true, dataIndex: "boitage", editor: new Ext.form.TextField({})},
            {header: "portions", width: 50, sortable: true, dataIndex: "portions", editor: new Ext.form.TextField({})},
            {header: "gamme", width: 50, sortable: true, dataIndex: "id_gamme", editor: combo_gamme},
            {header: "type", width: 50, sortable: true, dataIndex: "id_type", editor: combo_type}
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
collapsible: true,
            title: "Les produits",
            autoScroll: true,
            height: 300,
            store: store,
            stripeRows: true,
            plugins: [Ext.ux.grid.DataDrop,editor],
            columns : productColumns,

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
columnLines : true,
            iconCls: "icon-grid",
            frame: true,
            title: "Les produits",
            autoScroll: true,
            height: 300,
            store: store,
            stripeRows: true,
            plugins: [Ext.ux.grid.DataDrop,editor],
            columns : productColumns,
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
            view: new Ext.grid.GroupingView({
            forceFit:true,
            groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Items" : "Item"]})'
            })
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
}]
    });
window.show();
}

function onPrint() {

Ext.ux.Printer.print(grid);
}
        {/literal}]]></content>    </bloc>
</blocs>