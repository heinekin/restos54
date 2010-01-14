<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<blocs>
    <bloc type="page">
        <content method="innerHTML" id="main"><!--
            <h1>Suivi des livraisons</h1>
<table id="tableMain">
                <tr>
                    <td valign="top" id="leftMain">
<div id="suiviPanel"></div>
                    </td>
                    <td valign="top"><div id="rightMain"></div>
                    </td>
                </tr>
        </table>
        --></content>

    </bloc>
<bloc type="script">        <content><![CDATA[ {literal}

var colorMenu = new Ext.menu.ColorMenu({
        handler: function(cm, color){
            //Ext.example.msg('Color Selected', 'You chose {0}.', color);
        }
    });

var tb = new Ext.Toolbar();

    tb.add(
        new Ext.Toolbar.SplitButton({
            text: 'Split Button',
            handler: onButtonClick,
            tooltip: {text:'This is a an example QuickTip for a toolbar item', title:'Tip Title'},
            iconCls: 'blist',
            // Menus can be built/referenced by using nested menu config objects
            menu : {
                items: [{
                    text: '<b>Bold</b>', handler: onItemClick
                }, {
                    text: '<i>Italic</i>', handler: onItemClick
                }, {
                    text: '<u>Underline</u>', handler: onItemClick
                }, '-', {
                    text: 'Pick a Color',
                    handler: onItemClick,
                    menu: {
                        items: [
                            new Ext.ColorPalette({
                                listeners: {
                                    select: function(cp, color){
                                        //Ext.example.msg('Color Selected', 'You chose {0}.', color);
                                    }
                                }
                            }), '-',
                            {
                                text: 'More Colors...',
                                handler: onItemClick
                            }
                        ]
                    }
                }]
            }
        })
    );

function onButtonClick(btn){
        //Ext.example.msg('Button Click','You clicked the "{0}" button.', btn.text);
    }

    function onItemClick(item){
        //Ext.example.msg('Menu Click', 'You clicked the "{0}" menu item.', item.text);
    }








var store2 = new Ext.data.JsonStore({
        // store configs
        autoDestroy: true,
        url: 'ad54/stocks/loadsuivi',
        storeId: 'myStore2',
        // reader configs
        root: 'data',
        fields: ['id', {name:'reference',type:'int'}, 'nom', {/literal}{$this->monthfield()}{literal}  {name:'nb_colis',type:'int'}, {name:'total',type:'int'}, 'reste']
    });


var cellTips = new Ext.ux.plugins.grid.CellToolTips([
    	{ field: 'total', tpl: '<b>Formule:</b><br />Somme des livraisons de la campagne<br />' },
        { field: 'reste', tpl: '<b>Formule:</b><br />Reste à livrer. Entre parenthèses <br />les livraisons en surplus<br />' }
    ]);

    // manually load local data
    store2.load();









    var grid = new Ext.grid.GridPanel({
        columnLines : true,
        tbar: tb,
                columnLines : true,
                    id: 'grid_livraisons',
                store: store2,
        selModel: new Ext.grid.CellSelectionModel({
                            listeners:{'cellselect' : function( Sel, rowIndex, colIndex )
        {

        var class = grid.getView().getHeaderCell( colIndex ).className;
        class = class.substr(35, 10);
        if(class.substr(0, 9) == 'livraison')
        {
            var id = Sel.selection.record.data.id;
            var store3 = new Ext.data.JsonStore({
                // store configs
                //autoDestroy: true,
                url: 'ad54/stocks/loadpanel?id='+id+'&class='+class,
                //storeId: 'myStore3',
                // reader configs
                root: 'data',
                fields: ['id', {name:'reference',type:'int'}, 'nom', {name:'nb_colis',type:'int'}, {name:'mois',type:'date',dateFormat:'d/m/Y'}]
            });
            store3.load();
var grid3 = new Ext.grid.GridPanel({
            //id: 'grid_panel',
        store: store3,
        columns: [
            {header: 'id', sortable: true, dataIndex: 'id', hidden: true},
            {header: 'reference', sortable: true, dataIndex: 'reference'},
            {id: 'produit', header: 'produit', sortable: true, dataIndex: 'nom'},
            {header: 'date', renderer: Ext.util.Format.dateRenderer('d/m/Y'), sortable: true, dataIndex: 'mois'},
            {header: 'colis', sortable: true, dataIndex: 'nb_colis'}
        ],
        stripeRows: true,
            autoScroll: true,
            viewConfig: {
                sortAscText: 'Tri Ascendant',
                sortDescText: 'Tri Descendant',
                columnsText: 'Colonnes'
            }
    });

var w = new Ext.Window({
        title: 'Les livraisons du produit ref.' + Sel.selection.record.data.reference + ' pour le mois selectionné',
//autoHeight: true,
height: 200,
autoScroll: true,
        layout: 'fit',
       // plain:true,
        bodyStyle:'padding:5px;',
        buttonAlign:'center',
        items: [grid3],
        maximizable: true,
        collapsible: true
    });
    w.show();



        }

        }


         }}),
        width: 900,
        renderTo: 'suiviPanel',
        plugins: [cellTips],
        columns: [
            {header: 'id', sortable: true, dataIndex: 'id', hidden: true},
            {header: 'reference', sortable: true, width:50,dataIndex: 'reference'},
            {header: 'produit',width:100, sortable: true, dataIndex: 'nom'},
            {header: 'nb colis pour la campagne', sortable: true, dataIndex: 'nb_colis',
                renderer: function(v, params, record){
                if(!v) return 0;
                else    return v;
                            }},
            {/literal}{$this->monthcolumn()}{literal}
            {header: 'Total livraisons', css:'color:red; font-weight:bold;', sortable: true, dataIndex: 'total',renderer: function(v, params, record){
 return {/literal}{$this->mois()}{literal};
  }},
            {header: 'Reste à livrer', css:'color:blue;font-weight:bold;',sortable: true, dataIndex: 'reste',
renderer: function(v, params, record){
var output = record.data.nb_colis;
var  total = {/literal}{$this->mois()}{literal};
if(!output || output == 0 ) {
    if(total == 0 || !total) output = '0';
    else    output = '0 (-'+total+')';
}
else if((output < total )) output = 0 + ' (-'+total+')';
else output = (record.data.nb_colis - total);
//console.log(output);
return output;
  }}
        ],
height: 350,
            autoScroll: true,
frame: true,
        title: 'Suivi des livraisons par mois',
            viewConfig: {
                sortAscText: 'Tri Ascendant',
                sortDescText: 'Tri Descendant',
                columnsText: 'Colonnes'
            }
    });

        {/literal}]]></content>    </bloc>
</blocs>