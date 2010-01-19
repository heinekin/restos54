var MyTreeMenu = {
    mytree: null, 
    
    init:function(data) {
        this.mytree = new Ext.tree.TreePanel({
            el:'menu',
            useArrows:true,
            autoScroll:true,
            animate:true,
            enableDD:false,
            containerScroll: true,
            rootVisible:false,
            singleExpand:false,
            border: false,
            
            loader: new Ext.tree.TreeLoader({
    			preloadChildren: true,
    			clearOnLoad: false, 
                dataUrl: '/index/tree/'
    		}),
            
            root: {
                text: 'BOHD', 
                id: 'tree_0' 
            }
        });
        
        this.mytree.addListener('click', this.clickMenu);
    },

    clickMenu:function(arbre) {
        if(arbre.attributes.leaf) {
            MyTreeMenu.loadMenuBar(arbre);
        } else {
            MyTreeMenu.toogle(arbre);
        }
    },
    
    loadMenuBar:function(arbre){

        var loading = new Loading(1);
        
        MyContent.reset();
        new Ajax.Request('/index/menubar/node/' + arbre.id, 
            {
                method:'post',
                onCreate: function(req) {
                    loading.start();
                },
                onSuccess: function(req) {
                    var json = eval(req.responseJSON);
                    json.each(function(btn) {
                        if(btn.group) {
                            btn.menu.each(function(btn2) {
                                btn2.handler = MyMenuBar.clickMenu;
                            });
                        } else {
                            btn.handler = MyMenuBar.clickMenu;
                        }
                    });
                                        
                    if(MyMenuBar.panel !== null) {
                        MyMenuBar.destroy();
                    }
                    MyMenuBar.init(json);
                    MyMenuBar.render();
                    loading.stop();
                }
            });
    },
    
    toogle:function(arbre) {
        if(arbre.expanded) {
            arbre.collapse();
        } else {
            arbre.expand();
        }
    },
    
    render:function() {
        this.mytree.render();

        this.mytree.getRootNode().collapse();
        //this.mytree.expandAll();
    }
};