var MyMenuBar = {
    panel: null,
    
    init: function(toolBar) {
        
        this.panel = new Ext.Panel({
            el: 'navbar', 
            tbar: toolBar,
            border: false,
            shadow: false,
            height: 28
        });
    }, 
    
    render: function() {
        this.panel.render();
    },
    
    clickMenu: function(btn) {

        var loading = new Loading(1);

        new Ajax.Request(btn.link,
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
    },
    
    destroy: function() {
        $(this.panel.el.id).innerHTML = '';
    }
    
}