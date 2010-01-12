<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<blocs>
    <bloc type="page">
        <content method="innerHTML" id="main"><!--
            <h1>Statistiques sur les totaux de repas prevus et servis.</h1>
<h2>Vous pouvez afficher les détails de la semaine voulue en cliquant sur la barre correspondante</h2>
<table id="tableMain">
                <tr>
                    <td valign="top" id="leftMain">
<div id="quotas" style="width:610; margin: 0 auto;"></div>
<div id="chart"></div>
                    </td>
                </tr>
        </table>
<table>
                <tr>
                    <td valign="top">
<div id="my_chart"></div>
<td valign="top">
<div id="grid-example"></div></td>
</td></tr>
        </table>
        --></content>

    </bloc>
<bloc type="script">        <content><![CDATA[ {literal}
/*
{/literal}var nb_semaine = {$this->semaines()} - 1;{literal}

    // create the data store
    var store = new Ext.data.JsonStore({
        // store configs
        autoDestroy: true,
        url: 'ad54/quotas/loadquotas',
        storeId: 'myStore',
        // reader configs
        root: 'data',
        fields: ['centre', 'repartition', 'quotas']
    });

    // manually load local data
    store.load();

    // create the Grid
    var grid = new Ext.grid.GridPanel({
        store: store,
collapsible: true,
        columns: [
            {header: 'Centre', sortable: true, dataIndex: 'centre'},
            {header: 'Répartition', sortable: true, dataIndex: 'repartition'},
            {header: '% Quotas', sortable: true, dataIndex: 'quotas'}
        ],
        stripeRows: true,
height: 400,
            autoScroll: true,
frame: true,
        title: 'Les quotas de repas servis à la semaine ' + nb_semaine,
            viewConfig: {
                sortAscText: 'Tri Ascendant',
                sortDescText: 'Tri Descendant',
                columnsText: 'Colonnes'
            }
    });

    // render the grid to the specified div in the page
    grid.render('grid-example');


    new Ext.Panel({

        height: 400,
collapsible: true,
autofit: true,
        title: 'Diagramme des repas servis la semaine dernière',
        renderTo: 'quotas',
autoScroll: true,
        items: {
            store: store,
            xtype: 'piechart',
            dataField: 'repartition',
            categoryField: 'centre',
            //extra styles get applied to the chart defaults
            extraStyle:
            {
                legend:
                {
                    display: 'left',
                    padding: 5,
                    font:
                    {
                        family: 'Tahoma',
                        size: 7
                    }
                }
            }
        }
    });





// create the data store
    var store2 = new Ext.data.JsonStore({
        // store configs
        autoDestroy: true,
        url: 'ad54/quotas/loadtotal',
        storeId: 'myStore2',
        // reader configs
        root: 'data',
        fields: ['repas_prevus', 'repas_servis', 'semaine']
    });

    // manually load local data
    store2.load();



    new Ext.Panel({
        iconCls:'chart',
        title: 'Comparaison total repas servis/repas prevus par semaine',
        frame:true,
        renderTo: 'container',
        width:800,
        height:300,
        layout:'fit',

        items: {
            xtype: 'linechart',
            store: store2,
            url: 'swf/charts.swf',
            xField: 'semaine',
             //yField: 'comedy'      //Step 1
     series:[                //Step 2
         {yField:'repas_servis',displayName:'repas_servis'},  //Step 3
         {yField:'repas_prevus',displayName:'repas_prevus'}
     ],
    xAxis: new Ext.chart.CategoryAxis({
        labelRenderer:  this.customFormat
   }),
customFormat:function(value){
        return value+' sem';
    },
            yAxis: new Ext.chart.NumericAxis({
                displayName: 'repas_servis',
                labelRenderer : Ext.util.Format.numberRenderer('0,0')
            }),
            tipRenderer : function(chart, record, index, series){
                if(series.yField == 'repas_servis'){
                    return Ext.util.Format.number(record.data.repas_servis, '0,0') + ' repas servis à la semaine ' + record.data.semaine;
                }else{
                    return Ext.util.Format.number(record.data.repas_prevus, '0,0') + ' repas prévus à la semaine ' + record.data.semaine;
                }
            },
     extraStyle:{            //Step 1
         legend:{        //Step 2
             display: 'bottom'//Step 3
         }
     }
        }
    });*/



var swf = swfobject.embedSWF(
        "swf/open-flash-chart.swf",
        "chart",
        "600",
        "300",
        "9.0.0",
        "swf/expressInstall.swf",
        {
            "data-file": "ad54/quotas/loadofc",
            "loading": "Chargement en cours..."
        }
    );

    var panneau = new Ext.Panel({
        iconCls:'chart',
        collapsible: true,
        title: 'Comparaison total repas servis/repas prevus par semaine',
        frame:true,
        renderTo: 'quotas',
contentEl: 'chart',
        height:340,
        layout:'fit'/*,
tbar: [{
                text: "Imprimer",
                iconCls: "silk-printer",
                handler: onPrint
            }]*/
    });

/*function onPrint() {

Ext.ux.Printer.print(this);
}*/
        {/literal}]]></content>    </bloc>
</blocs>