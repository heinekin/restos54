<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<blocs>
    <bloc type="page">
        <content method="innerHTML" id="main"><!--
            <h1>Vous pouvez ici administrer les utilisateurs du système</h1>
<table id="tableMain">
                <tr>
                    <td valign="top" id="leftMain">
<div id="usergrid"></div>
<div id="userprint" style="display:none"></div>
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
var combo = new Ext.form.ComboBox({
    typeAhead: true,
    triggerAction: 'all',
    lazyRender:true,
    mode: 'local',
    store: new Ext.data.ArrayStore({
        id: 0,
        fields: [
            'id',
            'centre'
        ],
        data: {/literal}{$this->centres()}{literal}
    }),
    valueField: 'id',
    displayField: 'centre',
    hiddenName: 'centreID'


});

var combo2 = new Ext.form.ComboBox({
    typeAhead: true,
    triggerAction: 'all',
    lazyRender:true,
    mode: 'local',
    store: new Ext.data.ArrayStore({
        id: 0,
        fields: [
            'profile',
            'code'
        ],
        data: {/literal}{$this->profile()}{literal}
    }),
    valueField: 'profile',
    displayField: 'code',
    hiddenName: 'profileID'


});

var Usergrid = {

    app: null,
    proxy: null,
    reader: null,
    writer: null,
    store: null,
    userColumns: [],
    editor: null,
    grid: null,
    window: null,

    init: function(w)
    {
        this.initApp();
        this.initProxy();
        this.initRW();
        this.initStore();
        this.initColumns();
        this.store.load();
        this.initEditor();
        this.initGrid(w);
        this.initWindow();
        if(w == 1)
        {
            this.window.show();
        }
    },

    initApp: function()
    {
        this.app = new Ext.App({});
    },

    initProxy: function()
    {
        // Create a standard HttpProxy instance.
        this.proxy = new Ext.data.HttpProxy(
                           {url: '/ad54/rest',
                            listeners : {
                                write : function(store, action, result, response, rs) {
                                        Usergrid.app.setAlert(response.success, response.message);
                                        if(response.success != "Erreur !"){
                                        rs.commit();Usergrid.store.load();}
                }
                           }});
    },

    initRW: function()
    {
        // Typical JsonReader.  Notice additional meta-data params for defining the core attributes of your json-response
        this.reader = new Ext.data.JsonReader({
            totalProperty: "total",
            successProperty: "success",
            idProperty: "id",
            root: "data"
        }, [
            {name: "id"},
            {name: "Nom", allowBlank: false},
            {name: "Prenom", allowBlank: false},
            {name: "login", allowBlank: false},
            {name: "password", allowBlank: false},
            {name: "profile", allowBlank: false},
            {name: "centre", allowBlank: false}
            ]
        );

        // The new DataWriter component.
        this.writer = new Ext.data.JsonWriter({
            encode: true   // <-- don"t return encoded JSON -- causes Ext.Ajax#request to send data using jsonData config rather than HTTP params
        });
    },

    initStore: function()
    {
        // Typical Store collecting the Proxy, Reader and Writer together.
        this.store = new Ext.data.Store({
            id: "user",
            restful: true,     // <-- This Store is RESTful
            proxy: this.proxy,
            reader: this.reader,
            writer: this.writer

        });
    },

    initColumns: function()
    {
        // Let"s pretend we rendered our grid-columns with meta-data from our ORM framework.
        this.userColumns =  [
            {header: "ID", width: 40, sortable: true, dataIndex: "id"},
            {header: "Nom", width: 100, sortable: true, dataIndex: "Nom", editor: new Ext.form.TextField({})},
            {header: "Prenom", width: 50, sortable: true, dataIndex: "Prenom", editor: new Ext.form.TextField({})},
            {header: "Login", width: 50, sortable: true, dataIndex: "login", editor: new Ext.form.TextField({})},
            {header: "Password", width: 50, sortable: true, dataIndex: "password", editor: new Ext.form.TextField({})},
            {header: "Profil", width: 50, sortable: true, dataIndex: "profile", editor: combo2},
            {header: "Centre", width: 130, dataIndex: "centre", editor: combo}
        ];
    },

    initEditor: function()
    {
        // use RowEditor for editing
        this.editor = new Ext.ux.grid.RowEditor({
            saveText: "Valider",
            cancelText: "Annuler"
        });
    },

    initWindow: function()
    {
        this.window = new Ext.Window({
        title: 'Fenêtre volante',
        width: 750,
        height:400,
        minWidth: 300,
        minHeight: 200,
        layout: 'fit',
        plain:true,
        bodyStyle:'padding:5px;',
        buttonAlign:'center',
        items: this.grid
    });


    },

    initGrid: function(w)
    {
        if(w == 1){
        this.grid = new Ext.grid.GridPanel({
columnLines : true,
            id: 'grid1',
            iconCls: "icon-grid",
            frame: true,
            title: "Utilisateurs",
            autoScroll: true,
            height: 300,
            store: this.store,
            plugins: [this.editor],
            stripeRows: true,
            columns : this.userColumns,
            tbar: [{
                text: "Ajouter",
                iconCls: "silk-user-add",
                handler: onAdd
            }, "-", {
                text: "Supprimer",
                iconCls: "silk-user-delete",
                handler: onDelete
            }, "-", {
                text: "Modifier les droits",
                iconCls: "silk-key-add",
                handler: onRight
            }, {
                text: "Imprimer",
                iconCls: "silk-printer",
                handler: onPrint
            }, "-", {
                id: 'grid-excel-button',
                text: 'Ouvrir avec Excel',
                iconCls: "silk-page-excel",
                handler: function(){
                        document.location='data:application/vnd.ms-excel;base64,' + Base64.encode(Usergrid.grid.getExcelXml());
                }
    				}],
            viewConfig: {
                forceFit: true,
                sortAscText: 'Tri Ascendant',
                sortDescText: 'Tri Descendant',
                columnsText: 'Colonnes'
            }
        });
       }
       else
       {
       this.grid = new Ext.grid.GridPanel({
columnLines : true,
            id: 'grid1',
            renderTo: "usergrid",
            iconCls: "icon-grid",
            frame: true,
            title: "Utilisateurs",
            autoScroll: true,
            height: 300,
            store: this.store,
            stripeRows: true,
            plugins: [this.editor],
            columns : this.userColumns,
            tbar: [{
                text: "Ajouter",
                iconCls: "silk-user-add",
                handler: onAdd
            }, "-", {
                text: "Supprimer",
                iconCls: "silk-user-delete",
                handler: onDelete
            }, "-", {
                text: "Modifier les droits",
                iconCls: "silk-key-add",
                handler: onRight
            }, {
                text: "Ouvrir dans une fenêtre indépendante",
                iconCls: "silk-application-go",
                handler: onOpen
            }, "-", {
                text: "Imprimer",
                iconCls: "silk-printer",
                handler: onPrint
            }, "-", {
                id: 'grid-excel-button',
                text: 'Ouvrir avec Excel',
                iconCls: "silk-page-excel",
                handler: function(){
                        document.location='data:application/vnd.ms-excel;base64,' + Base64.encode(Usergrid.grid.getExcelXml());
                }
    				}],
            viewConfig: {
                forceFit: true,
                sortAscText: 'Tri Ascendant',
                sortDescText: 'Tri Descendant',
                columnsText: 'Colonnes'
            }
        });
       }

    }

}
Usergrid.init(0);
/**
 * onAdd
 */
function onAdd(btn, ev) {
    var u = new Usergrid.store.recordType({
        Nom : "",
        Prenom: "",
        login : "",
		password : "",
		profile: "",
                centre: ""
    });
    Usergrid.editor.stopEditing();
    Usergrid.grid.store.insert(0, u);
    Usergrid.editor.startEditing(0);
}
/**
 * onDelete
 */
function onDelete() {
    var rec = Usergrid.grid.getSelectionModel().getSelected();
    if (!rec) {
        return false;
    }
    Usergrid.grid.store.remove(rec);
}
function onOpen() {
   Usergrid.grid.destroy();
   Usergrid.init(1);
}

function onPrint() {

Ext.ux.Printer.print(Usergrid.grid);
}


/**
 * onRight
 */
function onRight() {
    var rec = Usergrid.grid.getSelectionModel().getSelected();
record_user = rec;
    if (!rec) {
        Ext.MessageBox.alert('Erreur', 'Sélectionnez d\'abord un utilisateur');
    }
    else
    {
       var loading = new Loading(1);
        new Ajax.Request('ad54/user/right?id='+rec.data.id,
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
}


        {/literal}]]></content>    </bloc>
</blocs>