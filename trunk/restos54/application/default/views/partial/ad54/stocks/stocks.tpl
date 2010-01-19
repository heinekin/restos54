<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<blocs>
    <bloc type="page">
        <content method="innerHTML" id="main"><!--
            <h1>Vous pouvez ici visualiser les stocks de l'entrepôt</h1>
            <h2>Les détails concernant les mouvements de stock s'affichent en cliquant sur la cellule du produit correspondant</h2>
<table id="tableMain">
                <tr>
                    <td valign="top" id="leftMain">
<div id="grid-example"></div>
<div id='panel'><div id="quotas"></div></div>
                    </td>
                </tr>
        </table>
        --></content>

    </bloc>
<bloc type="script">        <content><![CDATA[ {literal}
var store2 = new Ext.data.JsonStore({
        // store configs
        autoDestroy: true,
        url: 'ad54/stocks/loadstock',
        storeId: 'myStore2',
        // reader configs
        root: 'data',
        fields: ['id', {name:'reference',type:'int'}, 'nom', 'portions', {name:'conditionnement',type:'int'}, {name:'stock',type:'int'}, {name:'collecte',type:'int'}]
    });


/*var cellTips = new Ext.ux.plugins.grid.CellToolTips([
    	{ field: 'total', tpl: '<b>Formule:</b><br />Somme des livraisons de la campagne<br />' },
        { field: 'reste', tpl: '<b>Formule:</b><br />Reste à livrer. Entre parenthèses <br />les livraisons en surplus<br />' }
    ]);
*/
    // manually load local data
    store2.load();




    var grid = new Ext.grid.GridPanel({
        columnLines : true,
                columnLines : true,
                    id: 'grid_stock',
                        stripeRows: true,
                store: store2,
                autoFit:true,
                layout:'fit',
tbar: [{
            text: "Ouvrir dans une fenêtre indépendante",
            iconCls: "silk-application-go",
            handler: onOpen
        }],
        selModel: new Ext.grid.CellSelectionModel({
                            listeners:{cellselect : function( Sel, rowIndex, colIndex )
        {

        var class = grid.getView().getHeaderCell( colIndex ).className;

        if(class.include("stock") || class.include("collecte"))
        {

            if(class.include("stock"))
                var mouv = 'principal';
            else var mouv = 'collecte';
        var id = Sel.selection.record.data.id;
            var store3 = new Ext.data.JsonStore({
                // store configs
                //autoDestroy: true,
                url: 'ad54/stocks/loadstat?id='+id+'&class='+mouv,
                //storeId: 'myStore3',
                // reader configs
                root: 'data',
                fields: ['id',  'mouvement', {name:'reference',type:'int'}, 'nom', {name:'nb_colis',type:'int'}, {name:'mois',type:'date',dateFormat:'d/m/Y'}]
            });
            store3.load();
var grid3 = new Ext.grid.GridPanel({
            //id: 'grid_panel',
 columnWidth: 0.50,
height: 300,
        store: store3,
        columns: [
            {header: 'id', sortable: true, dataIndex: 'id', hidden: true},
            {header: 'mouvement', width:230, sortable: true, dataIndex: 'mouvement',renderer: function(val, params, record){
                if(val.include("sortie")) return '<span style="color:red;">' + val + '</span>';
                else    return '<span style="color:green;">' + val + '</span>';
                            }},
            {header: 'reference', sortable: true, dataIndex: 'reference', hidden: true},
            {id: 'produit', header: 'produit', sortable: true, dataIndex: 'nom', hidden: true},
            {header: 'date', renderer: Ext.util.Format.dateRenderer('d/m/Y'), sortable: true, dataIndex: 'mois'},
            {header: 'colis', sortable: true, dataIndex: 'nb_colis'}
        ],
        stripeRows: true,
            autoScroll: true,
            viewConfig: {
                forceFit: true,
                sortAscText: 'Tri Ascendant',
                sortDescText: 'Tri Descendant',
                columnsText: 'Colonnes'
            }
    });
$('panel').innerHTML = $('panel').innerHTML +="<div id='chart'></div>";

var swf = swfobject.embedSWF(
        "swf/open-flash-chart.swf",
        "chart",
        "400",
        "300",
        "9.0.0",
        "swf/expressInstall.swf",
        {
            "data-file": "ad54/stocks/loadofc?id="+id+"_"+mouv,
            "loading": "Chargement en cours..."
        }
    );


var myMapPanel= new Ext.Panel({
 columnWidth: 0.50,
  contentEl: 'chart'
});
/*
var window = new Ext.Window({
title: 'My AM Map',
items: myMapPanel
}).show();*/
/*var panneau = new Ext.Panel({
        iconCls:'chart',
        collapsible: true,
        title: 'Comparaison total repas servis/repas prevus par semaine',
        frame:true,
        renderTo: 'quotas',
contentEl: 'chart',
        height:340,
        layout:'fit'
    });*/

var w = new Ext.Window({
        title: 'Les mouvements du stock \''+mouv+'\' -- Produit ref.' + Sel.selection.record.data.reference + ' '+Sel.selection.record.data.nom ,
autoHeight: true,
height: 300,
autoScroll: true,
        layout: 'fit',
       // plain:true,
        bodyStyle:'padding:5px;',
        buttonAlign:'center',
        layout: 'column',
        items: [grid3,myMapPanel],
        maximizable: true,
        collapsible: true
    });
    w.show();
}


        }


         }}),
        width: 900,
        renderTo: 'grid-example',
        //plugins: [cellTips],
        columns: [
            {header: 'id', sortable: true, dataIndex: 'id', hidden: true},
            {header: 'reference', sortable: true, dataIndex: 'reference'},
            {id: 'produit', header: 'produit', sortable: true, dataIndex: 'nom'},
            {header: 'portions', sortable: true, dataIndex: 'portions', hidden: true},
            {header: 'conditionnement', sortable: true, dataIndex: 'conditionnement',hidden: true},
            {id: 'stock', header: 'stock', sortable: true, dataIndex: 'stock',
                renderer: function(v, params, record){
                if(!v) return 0;
                else    return v;
                            }},
            {id: 'collecte', header: 'collecte', sortable: true, dataIndex: 'collecte',
                renderer: function(v, params, record){
                if(!v) return 0;
                else    return v;
                            }}
        ],
height: 350,
            autoScroll: true,
frame: true,
        title: 'Stock actuel à l\'entrepôt',
            viewConfig: {
                forceFit:true,
                sortAscText: 'Tri Ascendant',
                sortDescText: 'Tri Descendant',
                columnsText: 'Colonnes'
            }
    });

function onOpen()
{
    grid.destroy();
}

        {/literal}]]></content>    </bloc>
</blocs>