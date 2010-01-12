Ext.onReady(function(){
    Ext.BLANK_IMAGE_URL = 'js/ext/resources/images/default/s.gif';
    MyTreeMenu.init();
    MyTreeMenu.render();
    Ext.QuickTips.init();
    var simple = new Ext.FormPanel({
        border: false,
        items: [{xtype:'themecombo',width:150}
        ]
    });
    simple.render('menu_theme');

    var spot = new Ext.ux.Spotlight({
        easing: 'easeOut',
        duration: .8
    });
    var user = $('user').innerHTML.substr(0, $('user').innerHTML.length -1);
 var DemoPanel = Ext.extend(Ext.Panel, {
        title: 'M. '+user,
        frame: true,
        width: 200,
        height: 150,
        html: 'Bienvenue dans le système d\'information des restos du coeur !',
        bodyStyle: 'padding:10px 15px;',

        toggle: function(on){
            this.buttons[0].setDisabled(!on);
        }
    });

    var p1;
    var updateSpot = function(id){
        if(typeof id == 'string'){
            spot.show(id);
        }else if (!id && spot.active){
            spot.hide();
            panel.destroy();
        }
        p1.toggle(id==p1.id);
    };

    var panel = new Ext.Panel({
        renderTo: Ext.getBody(),
        layout: 'table',
        id: 'demo-ct',
        border: false,
        floating: true,
        items: [p1 = new DemoPanel({
            id: 'panel1',
            buttons: [{
                text: 'Fermer',
                handler: updateSpot.createDelegate(this, [false])
            }]
        })]
    });
    var bodyDim = $(document.body).getDimensions();
    panel.setPosition(bodyDim.width/2.5, bodyDim.height/2.5)
    updateSpot('panel1');
var record_user = null;

});


function barClicked(index, onClickText)
{
    index = index + 1;
    
   var type = onClickText;
if(type == 'servis_old') type = 'servis l\'année dernière';
if(Ext.get('grid_quotas') != null)
    {
        Ext.get('grid_quotas').remove();
        Ext.get('chart_quotas').remove();
    }
   var store = new Ext.data.JsonStore({
        // store configs
        autoDestroy: true,
        url: 'ad54/quotas/loadquotas?index='+index+'&type='+onClickText,
        storeId: 'myStore',
        // reader configs
        root: 'data',
        fields: ['centre', 'repartition', 'quotas']
    });

    // manually load local data
    store.load();
        var grid = new Ext.grid.GridPanel({
            id: 'grid_quotas',
        store: store,
        renderTo: 'grid-example',
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
        title: 'Les quotas de repas ' + type + ' à la semaine ' + index,
            viewConfig: {
                sortAscText: 'Tri Ascendant',
                sortDescText: 'Tri Descendant',
                columnsText: 'Colonnes'
            }
    });
       var chart = new Ext.Panel({
           id: 'chart_quotas',
           renderTo: 'my_chart',
        height: 400,
        width: 600,
collapsible: true,
autofit: true,
        title: 'Diagramme des repas ' + type + ' à la semaine ' + index,
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

Ext.get('tableMain').highlight("D8E6F6");

  /* var window = new Ext.Window({
        title: 'Les quotas de la semaine' + index,
        width: 750,
        height:400,
        minWidth: 300,
        minHeight: 400,
        layout: 'fit',
        plain:true,
        bodyStyle:'padding:5px;',
        buttonAlign:'center',
        items: [grid],
        maximizable: true,
        collapsible: true
    });
    window.show();*/

}

function xmlResponse(dom) {
    blocs = dom.getElementsByTagName('bloc');
    for(var i = 0 ; i < blocs.length ; i++) {
        if(blocs[i].attributes.length == 1) {
            switch(blocs[i].attributes[0].nodeValue) {
                case 'page':
                    //alert('page');
                    xmlResponsePage(blocs[i].getElementsByTagName('content')[0]);
                    break;
                    
                case 'script':
                    //alert('script');
                    xmlResponseScript(blocs[i].getElementsByTagName('content')[0]);
                    break;
                    
                case 'json':
                    //alert('json');
                    xmlResponseJson(blocs[i].getElementsByTagName('content')[0]);
                    break;
            }
        }
    }
}

function xmlResponsePage(page) {
        page_method = '';
        page_id = '';
        for (var j = 0 ; j < page.attributes.length ; j ++) {
            if(page.attributes[j].nodeName == 'method') {
                page_method = page.attributes[j].nodeValue;
            } else if(page.attributes[j].nodeName == 'id') {
                page_id = page.attributes[j].nodeValue;
            }
        }
        if(page_method != '' && page_id != '') {
            var element = $(page_id);
            var value = page.firstChild.data;
            switch(page_method) {
                case 'innerHTML':
                    element.innerHTML = value;
                    break;
                case 'value':
                    element.value = value;
                    break;
            }
            
        }
}

function xmlResponseScript(script) {
    eval(script.firstChild.data);
}

function xmlResponseJson(json) {
        variable = '';
        for (var j = 0 ; j < json.attributes.length ; j ++) {
            if(json.attributes[j].nodeName == 'variable') {
                variable = json.attributes[j].nodeValue;
                break;
            }
        }
        if(variable != '') {
            var value = json.firstChild.data;
            str_eval = 'var ' + variable + ' = ' + value + ';';
            eval(str_eval);
        }
}

function submitFormAjax(form) {
    var loading = new Loading(1);
    new Ajax.Request(form.action,
        {
            method:form.method, 
            parameters: form.serialize(true), 
            onCreate: function(req) {
                loading.start();
            },
            onSuccess: function(req) {
                loading.stop();
                xmlResponse(req.responseXML);
            } 
        });
}

function sendQueryAjax(query) {
    var loading = new Loading(1);
    new Ajax.Request(query,
        {
            method:'GET',
            onCreate: function(req) {
                loading.start();
            },
            onSuccess: function(req) {
                loading.stop();
                xmlResponse(req.responseXML);
            } 
        });
}

function datePicker(idChp, dateFormat) {
    this.idChp = idChp;
    this.dateFormat = dateFormat;
    this.eltRender = document.createElement('div');
    this.eltRender.id = 'calendar_' + this.idChp;

    var obj = this;
    this.myDP = new Ext.DatePicker(
    {
        startDay: 1,
        hidden: true,
        listeners: {
            'select': function(myDP, date) {
                var field = $(obj.idChp);
                field.value = date.format(obj.dateFormat);
                obj.hide();
            }
        }
    });

    this.render = function() {
        $(this.idChp).parentNode.appendChild(this.eltRender);
        this.myDP.render(this.eltRender.id);
        this.changeStyle();
    };

    this.changeStyle = function() {
        this.eltRender.style.position = 'absolute';
        this.eltRender.style.zIndex = 15000;
    };

    this.hide = function() {
        this.myDP.hide();
    };

    this.show = function() {
        this.myDP.show();
    };

    this.isVisible = function() {
        return this.myDP.isVisible();
    }

    this.toggle = function() {
        if(this.isVisible()) {
            this.hide();
        } else {
            this.show();
        }
    };
    this.render();
}