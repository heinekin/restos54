<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<blocs>
    <bloc type="page">
        <content method="innerHTML" id="main"><!--
            <h1>Vous pouvez ici rentrer les repas prévus et servis par semaine</h1>
<table id="tableMain">
                <tr>
                    <td valign="top" id="leftMain" style="width:800px">
<div id="addtab"></div></br>
<div id="tabs1"></div>
                    </td>
                    <td valign="top"><div id="rightMain" />
                    </td>
                </tr>
        </table>
        --></content>

    </bloc>
<bloc type="script">        <content><![CDATA[ {literal}
{/literal}var nb_semaine = {$this->semaines()};{literal}


var tabs = new Ext.TabPanel({
        renderTo:'tabs1',
        resizeTabs:true, // turn on tab resizing
        minTabWidth: 115,
        tabWidth:135,
        enableTabScroll:true,
        animScroll: true,
        width:800,
        height:521,
        defaults: {autoScroll:true}
    });

Ext.MessageBox.buttonText.yes = "Oui";
Ext.MessageBox.buttonText.no = "Non";
    // tab generation code
    var index = 0;
    while(index < nb_semaine){
        tabs.add({
            title: 'Semaine ' + (++index),
            iconCls: 'tabs',
            html: '<div class="onglet" id="onglet' + (index) + '"></div>',
            closable:false
        }).show();
        loadGrid();
    }
    function addTab(){
if (nb_semaine < 17){
Ext.Msg.show({
   title:'Confirmation',
   msg: 'Voulez-vous commencer une nouvelle semaine?',
   buttons: Ext.Msg.YESNO,
   fn: addTab2,
   animEl: 'elId'
});
}else{
Ext.Msg.show({
   title:'Confirmation',
   msg: 'La semaine 17 est en cours, voulez-vous commencer une nouvelle semaine?',
   buttons: Ext.Msg.YESCANCEL,
   fn: addTab2,
   animEl: 'elId'
});
}
    }
function addTab2(btn){
if(btn == 'yes'){
tabs.add({
            title: 'Semaine ' + (++index),
            iconCls: 'tabs',
            html: '<div id="onglet' + (index) + '"></div>',
            closable:false
        }).show();
        loadGrid();
}
}
    new Ext.Button({
        text: 'Nouvelle Semaine',
        handler: addTab,
        iconCls:'silk-add'
    }).render(document.body, 'addtab');

function total(v, params, data) {
            params.attr = 'ext:qtip="Totaux"'; // summary column tooltip example
            return 'TOTAUX';
    }
function pourcent(val){

            return val + ' %';

    }

function loadGrid()
{
var grid = null;
var summary = new Ext.ux.grid.GridSummary();
var app = new Ext.App({});
var proxy = new Ext.data.HttpProxy(
                           {url: '/ad54/restbnf/'+index,
                            listeners : {
                                write : function(store, action, result, response, rs) {
                                        app.setAlert(response.success, response.message);
                                        if(response.success != "Erreur !"){
                                        rs.commit();grid.store.load();}
                }
                           }});

var MyRecord = Ext.ux.data.CalcRecord.create([
            {name: "id"},
            {name: "centre"},
            {name: "repartition", allowBlank: false},
            {name: "parts", allowBlank: false},
            {name: "repas_prevus", allowBlank: false},
            {name: "repas_servis", allowBlank: false},
            {name: "decalage", dependencies: ['repas_prevus', 'repas_servis'], notDirty: true, calc: function(record) {
		return (record.get('repas_prevus') - record.get('repas_servis'));
	}},
            {name: 'rapport', dependencies: ['repas_prevus', 'repas_servis'], notDirty: true, calc: function(record) {
		return parseInt(((record.get('repas_servis') / record.get('repas_prevus')) - 1) * 100);
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
            id: "bnf"+index,
            restful: true,     // <-- This Store is RESTful
            proxy: proxy,
            reader: reader,
            writer: writer,
sortInfo:{field: 'id', direction: "ASC"},
groupField:''

        });


var productColumns =  [
            new Ext.grid.RowNumberer(),
            {header: "id", summaryRenderer: total, sortable: true, dataIndex: "id"},
            {header: "centre", sortable: true, dataIndex: "centre"},
            {header: "repartition", renderer: pourcent, summaryType: 'sum',sortable: true, dataIndex: "repartition"},
            {header: 'parts', summaryType: 'sum',dataIndex: 'parts', sortable: true},
            {header: 'prevus', summaryType: 'sum', css: '{color:green;}', dataIndex: 'repas_prevus', sortable: true, editor: new Ext.form.NumberField({})},
            {header: "servis", summaryType: 'sum', css: '{color:blue;}', sortable: true, dataIndex: "repas_servis", editor: new Ext.form.NumberField({})},
            {header: "decalage", summaryType: 'sum',sortable: true, dataIndex: "decalage"},
            {header: 'rapport', renderer: pourcent, summaryType: 'sum',dataIndex: 'rapport', sortable: true}

];

store.load();

var cellTips = new Ext.ux.plugins.grid.CellToolTips([
    	{ field: 'decalage', tpl: '<b>Formule:</b><br />prevus - servis<br />' },
        { field: 'rapport', tpl: '<b>Formule:</b><br />servis/prevus - 1<br />' }
    ]);




var editor = new Ext.ux.grid.RowEditor({
            saveText: "Valider",
            cancelText: "Annuler"
        });

grid = new Ext.grid.GridPanel({

            iconCls: "icon-grid",
            frame: true,
            title: "Les bénéficiaires",
            autoScroll: true,
            height: 500,
            store: store,
            stripeRows: true,
            plugins: [editor,cellTips,summary],
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




grid.render('onglet'+index);
summary.toggleSummary(true);
}
function onOpen() {
grid.hide();
var grid2 = new Ext.grid.GridPanel({
            iconCls: "icon-grid",
            autoScroll: true,
            height: 300,
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
new Ext.Button({
        renderTo: 'toggleSummary',
        text: 'Toggle Summary',
        handler: function(btn, e) {
            summary.toggleSummary();
        }
    });
        {/literal}]]></content>    </bloc>
</blocs>