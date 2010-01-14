<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<blocs>
    <bloc type="page">
        <content method="innerHTML" id="main"><!--
            <h1>Vous pouvez ici visualiser et modifier les stocks </h1>
<table id="tableMain">
                <tr>
                    <td valign="top" id="leftMain" style="width:300px">
<div id="tabs1">
        

    </div>
                    </td>
                    <td valign="top"><div id="rightMain"  style="width:400px"></div>
                    </td>
                </tr>
        </table>
        --></content>

    </bloc>
<bloc type="script">        <content><![CDATA[ {literal}

var tabs = new Ext.TabPanel({
        renderTo:'tabs1',
        resizeTabs:true, // turn on tab resizing
        minTabWidth: 115,
        tabWidth:135,
        enableTabScroll:true,
        width:800,
        height:250,
        defaults: {autoScroll:true}//,
        //plugins: new Ext.ux.TabCloseMenu()
    });

    // tab generation code
    var index = 0;
    while(index < 3){
        addTab();
    }
    function addTab(){
        tabs.add({
            title: 'New Tab ' + (++index),
            iconCls: 'tabs',
            html: 'div1 <div id="prout' + (index) + '"></div>',
listeners: {activate: loadGrid},

            closable:true
        }).show();
    }

    new Ext.Button({
        text: 'Add Tab',
        handler: addTab,
        iconCls:'new-tab'
    }).render(document.body, 'tabs1');


function loadGrid()
{
var loading = new Loading(1);
loading.start();
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
            if(record.isModified('nb_colis'))
		return '<span style="color:red;">' + (record.get('inventaire') + record.get('nb_colis')) * record.get('conditionnement') +'</span>';

            else
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
            writer: writer

        });

columnQTipRenderer = function(val, meta, record, ind, colInd){
        meta.attr = 'ext:qtip="' + val + '" ext:anchorToTarget=true';
        return val;

    };

  function pctChange(val){

            return '<span style="color:red;">' + val + '</span>';

    }

var productColumns =  [
            {header: "id", sortable: true, dataIndex: "id"},
            {header: "reference", renderer: columnQTipRenderer, sortable: true, dataIndex: "reference"},
            {header: "nom", sortable: true, dataIndex: "nom"},
            {header: 'conditionnement', dataIndex: 'conditionnement', sortable: true},
            {header: 'portions', dataIndex: 'portions', sortable: true},
            {header: "nb de colis attribués", css: '{color:red;}', sortable: true, dataIndex: "nb_colis", editor: new Ext.form.NumberField({})},
            {header: "inventaire fin campagne précedente", sortable: true, dataIndex: "inventaire"},
            {header: 'Total Boites ou Pack', dataIndex: 'sum', sortable: true}

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
            title: "Les prévisions",
            autoScroll: true,
            height: 500,
            store: store,
            stripeRows: true,
            plugins: [Ext.ux.grid.DataDrop,editor],
            columns : productColumns,
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
grid.render('prout'+index);
loading.stop();
}

function onPrint() {

Ext.ux.Printer.print(grid);
}


 // eof


        {/literal}]]></content>    </bloc>
</blocs>