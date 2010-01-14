<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<blocs>
    <bloc type="page">
        <content method="innerHTML" id="main"><!--
            <h1>Entrer une nouvelle livraison</h1>
<table id="tableMain">
                <tr>
                    <td valign="top" id="leftMain">
<div id="grid-example"></div>
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
var test = 0;
var store = new Ext.data.JsonStore({
        autoDestroy: true,
        url: 'ad54/stocks/loadproduct',
        storeId: 'myStore',
        root: 'data',
        fields: ['id', 'reference', 'nom']
    });

    // manually load local data
    store.load();



    // example of custom renderer function
    function italic(value){
        return '<i>' + value + '</i>';
    }

    // example of custom renderer function
    function change(val){
        if(val > 0){
            return '<span style="color:green;">' + val + '</span>';
        }else if(val < 0){
            return '<span style="color:red;">' + val + '</span>';
        }
        return val;
    }
    // example of custom renderer function
    function pctChange(val){
        if(val > 0){
            return '<span style="color:green;">' + val + '%</span>';
        }else if(val < 0){
            return '<span style="color:red;">' + val + '%</span>';
        }
        return val;
    }

/*
    // the DefaultColumnModel expects this blob to define columns. It can be extended to provide
    // custom or reusable ColumnModels
    var colModel = new Ext.grid.ColumnModel([
        {id:'company',header: "Company", width: 160, sortable: true, locked:false, dataIndex: 'company'},
        {header: "Price", width: 55, sortable: true, renderer: Ext.util.Format.usMoney, dataIndex: 'price'},
        {header: "Change", width: 55, sortable: true, renderer: change, dataIndex: 'change'},
        {header: "% Change", width: 65, sortable: true, renderer: pctChange, dataIndex: 'pctChange'},
        {header: "Last Updated", width: 80, sortable: true, renderer: Ext.util.Format.dateRenderer('m/d/Y'), dataIndex: 'lastChange'}
    ]);*/
    var colModel = new Ext.grid.ColumnModel([
        {id:'company',header: "id", width: 40, sortable: true, dataIndex: 'id'},
        {header: "reference", width: 80, sortable: true, dataIndex: 'reference'},
        {header: "produit",  sortable: true, dataIndex: 'nom'}
    ]);

/*
 *    Here is where we create the Form
 */
    var gridForm = new Ext.FormPanel({
        id: 'company-form',
        buttons: [{
            id: 'save',
            text: 'Sauvegarder',
            handler: function(){
                    gridForm.getForm().submit({url:'ad54/stocks/savelivraison', waitMsg:'Sauvegarde de la livraison...', success: function(){store2.reload();}});
}

        }],
        frame: true,
        labelAlign: 'left',
        title: 'Nouvelle livraison',
        bodyStyle:'padding:5px',
        width: 550,
        layout: 'column',    // Specifies that the items will now be arranged in columns
        items: [{
            columnWidth: 0.45,
            layout: 'fit',
            items: {
                xtype: 'grid',
                ds: store,
                cm: colModel,
                sm: new Ext.grid.RowSelectionModel({
                    singleSelect: true,
                    listeners: {
                        rowselect: function(sm, row, rec) {
                            Ext.getCmp("company-form").getForm().loadRecord(rec);
                            Ext.get('save').show();
                        }
                    }
                }),
                height: 200,
                title:'Choisir un produit dans la liste',
                border: true,
                listeners: {
                    viewready: function(g) {
                        g.getSelectionModel().selectRow(0);
                    } // Allow rows to be rendered.
                }
            }
        },{
            columnWidth: 0.55,
            xtype: 'fieldset',
            labelWidth: 90,
            title:'Détails de la livraison',
            defaults: {width: 140, border:false},    // Default config options for child items
            defaultType: 'textfield',
            autoHeight: true,
            bodyStyle: Ext.isIE ? 'padding:0 0 5px 15px;' : 'padding:10px 15px;',
            border: false,
            style: {
                "margin-left": "10px", // when you add custom margin in IE 6...
                "margin-right": Ext.isIE6 ? (Ext.isStrict ? "-10px" : "-13px") : "0"  // you have to adjust for it somewhere else
            },
            items: [{
                xtype: 'hidden',
                fieldLabel: 'id',
                name: 'id',
                allowBlank: false
            },{
                xtype: 'displayfield',
                fieldLabel: 'reference',
                name: 'reference'
            },{
                xtype: 'displayfield',
                fieldLabel: 'nom',
                name: 'nom'
            },{
                xtype: 'datefield',
                fieldLabel: 'Date',
                name: 'date',
                format : 'd/m/Y',
                allowBlank: false,
                minValue: '{/literal}{$this->campagne()}{literal}',
                minText: 'La date doit être supérieur ou égale à la date de lancement de la campagne le {0}',
                blankText: 'Vous devez renseigner la date de la livraison'
            },{
                xtype: 'numberfield',
                fieldLabel: 'colis livrés',
                name: 'nb_colis',
                allowBlank: false,
                blankText: 'Vous devez renseigner le nombre de colis livré'
            }]
        }],
        renderTo: 'grid-example'
    });
Ext.get('save').hide();
var store2 = new Ext.data.JsonStore({
        // store configs
        autoDestroy: true,
        url: 'ad54/stocks/loadlivraison',
        storeId: 'myStore2',
        // reader configs
        root: 'data',
        fields: ['id', 'reference', 'nom', {name: 'date', type: 'date',dateFormat: 'd/m/Y'}, 'nb_colis'],
        listeners: {load : function( Store, records, options ){
if(test != 0){
var g = $('rightMain');

g.highlight();
}else{
test=1;
}
 }
    }});

    // manually load local data
    store2.load();
        var grid = new Ext.grid.GridPanel({
columnLines : true,
            id: 'grid_livraisons',
        store: store2,
width: 450,
        renderTo: 'livraisonsPanel',
        columns: [
            {header: 'id', sortable: true, dataIndex: 'id', hidden: true},
            {header: 'reference', sortable: true, dataIndex: 'reference'},
            {id: 'produit', header: 'produit', sortable: true, dataIndex: 'nom'},
            {header: 'date', renderer: Ext.util.Format.dateRenderer('d/m/Y'), sortable: true, dataIndex: 'date'},
            {header: 'colis', sortable: true, dataIndex: 'nb_colis'}
        ],
autoExpandColumn: 'produit',
height: 280,
            autoScroll: true,
frame: true,
        title: 'Les livraisons reçues',
            viewConfig: {
                sortAscText: 'Tri Ascendant',
                sortDescText: 'Tri Descendant',
                columnsText: 'Colonnes'
            }
    });


        {/literal}]]></content>    </bloc>
</blocs>