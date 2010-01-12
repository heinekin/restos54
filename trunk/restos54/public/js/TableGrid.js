Ext.grid.TableGrid = function(table, config) {
  config = config || {};
  Ext.apply(this, config);
  var cf = config.fields || [], ch = config.columns || [];
  table = Ext.get(table);

  var ct = table.insertSibling();

  var fields = [], cols = [];
  var headers = table.query("thead th");
  
  for (var i = 0, h; h = headers[i]; i++) {
    var text = h.innerHTML;
    var name = 'tcol-'+i;
    
    hatts = $A(h.attributes);
    
    // vérifie si la colonne est triable
    var node_sortable = hatts.find(function(att) {
        return (att.nodeName == 'sortable');
    });
    var sortable = false;
    if('undefined' != typeof node_sortable) {
        sortable = eval(node_sortable.nodeValue);
    }
    
    // vérifie si la colonne est resizable
    var node_resizable = hatts.find(function(att) {
        return (att.nodeName == 'resizable');
    });
    var resizable = false;
    if('undefined' != typeof node_resizable) {
        resizable = eval(node_resizable.nodeValue);
    }
    
    // vérifie si la colonne contient un tooltip
    var node_tooltip = hatts.find(function(att) {
        return (att.nodeName == 'tooltip');
    });
    var tooltip = false;
    if('undefined' != typeof node_tooltip) {
        tooltip = node_tooltip.nodeValue;
    }
    
    // vérifie si la colonne est fixe
    var node_fixed = hatts.find(function(att) {
        return (att.nodeName == 'fixed');
    });
    var fixed = false;
    if('undefined' != typeof node_fixed) {
        fixed = eval(node_fixed.nodeValue);
    }

    // vérifie si la colonne a une taille
    var node_width = hatts.find(function(att) {
        return (att.nodeName == 'width');
    });
    var width = h.offsetWidth;
    if('undefined' != typeof node_width) {
        width = eval(node_width.nodeValue);
    }

    // vérifie l'alignement
    var node_align = hatts.find(function(att) {
        return (att.nodeName == 'align');
    });
    var css = '';
    if('undefined' != typeof node_align) {
        css = 'text-align: ' + node_align.nodeValue + ';';
    }

    // vérifie si il y a une fonction de rendu
    /*var node_renderer = hatts.find(function(att) {
        return (att.nodeName == 'renderer');
    });*/
    /*var renderer = 'totogrid';*/

    // vérifie si la colonne a un type
    var node_type = hatts.find(function(att) {
        return (att.nodeName == 'type');
    });
    var field_type = 'auto';
    if('undefined' != typeof node_type) {
        field_type = node_type.nodeValue;
    }
    // si le type est date, on fixe le format au timestamp
    var dateFormat = '';
    if(field_type == 'date') {
        dateFormat = 'timestamp';
    }

    fields.push(Ext.applyIf(cf[i] || {}, {
      name: name,
      type: field_type,
      dateFormat: dateFormat, 
      mapping: 'td:nth('+(i+1)+')/@innerHTML'
    }));

    if(field_type == 'date') {
        cols.push(Ext.applyIf(ch[i] || {}, {
          'header': text,
          'dataIndex': name,
          'width': width,
          'tooltip': h.title,
          'sortable': sortable,
          'resizable': sortable,
          'fixed': fixed,
          'css': css,
          'renderer': Ext.util.Format.dateRenderer('m/d/Y')
        }));
    }
    else
    {
        cols.push(Ext.applyIf(ch[i] || {}, {
          'header': text,
          'dataIndex': name,
          'width': width,
          'tooltip': h.title,
          'sortable': sortable,
          'resizable': sortable,
          'fixed': fixed,
          'css': css
        }));
    }
  }

  var ds  = new Ext.data.Store({
    reader: new Ext.data.XmlReader({
      record:'tbody tr'
    }, fields)
  });

  ds.loadData(table.dom);

  var cm = new Ext.grid.ColumnModel(cols);

  if (config.width || config.height) {
    ct.setSize(config.width || 'auto', config.height || 'auto');
  } else {
    ct.setWidth(table.getWidth());
  }

  if (config.remove !== false) {
    table.remove();
  }

  Ext.applyIf(this, {
    'ds': ds,
    'cm': cm,
    'sm': new Ext.grid.RowSelectionModel(),
    autoHeight: false,
    autoWidth: false
  });

  Ext.grid.TableGrid.superclass.constructor.call(this, ct, {});
};

Ext.extend(Ext.grid.TableGrid, Ext.grid.GridPanel);