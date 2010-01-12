<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<blocs>
    <bloc type="page">
        <content method="innerHTML" id="main"><!--
            <h1>Vous pouvez ici administrer les utilisateurs du système</h1>
            <table id="tableMain">
                <tr>
                    <td valign="top" id="usergrid">
                    </td>
            </table>
        --></content>

    </bloc>
   <bloc type="script">
        <content><![CDATA[ {literal}
var Usergrid = {

    app: null,
    proxy: null,
    reader: null,
    writer: null,
    store: null,
    userColumns: [],
    editor: null,
    grid: null,

    init: function()
    {
        this.initApp();
        this.initProxy();
        this.initRW();
        this.initStore();
        this.initColumns();
        this.store.load();
        this.initEditor();
        this.initGrid();

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
                                        rs.commit();}
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
            {name: "su", allowBlank: false}
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
            {header: "Administrateur", width: 50, sortable: true, dataIndex: "su", editor: new Ext.form.Checkbox()}
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

    initGrid: function()
    {
        this.grid = new Ext.grid.GridPanel({
            renderTo: "usergrid",
            iconCls: "icon-grid",
            frame: true,
            title: "Utilisateurs",
            autoScroll: true,
            height: 300,
            store: this.store,
            plugins: [this.editor],
            columns : this.userColumns,
            tbar: [{
                text: "Ajouter",
                iconCls: "silk-add",
                handler: onAdd
            }, "-", {
                text: "Supprimer",
                iconCls: "silk-delete",
                handler: onDelete
            }, "-", {
                text: "Modifier les droits",
                iconCls: "silk-key-add",
                handler: onRight
            }],
            viewConfig: {
                forceFit: true
            }
        });
    }





}
Usergrid.init();
/**
 * onAdd
 */
function onAdd(btn, ev) {
    var u = new Usergrid.store.recordType({
        Nom : "",
        Prenom: "",
        login : "",
		password : "",
		su: ""
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

/**
 * onRight
 */
function onRight() {
    var rec = Usergrid.grid.getSelectionModel().getSelected();
console.log(rec);
    if (!rec) {
        return false;
    }
    //Usergrid.grid.store.remove(rec);
}

        {/literal}]]></content>
    </bloc>
</blocs>