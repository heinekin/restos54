var FeatureList = {
    mytree: null, 
    selectedFeature: 0,
    
    init: function() {
        btnAdd = new Ext.Button({
                text:'Ajouter',
                tooltip:'Ajoute une nouvelle fonction'
            });
        btnAdd.addListener('click', FeatureList.add);
        
        btnModify = new Ext.Button({
                text:'Modifier',
                tooltip:'Modifie la fonction selectionnée'
            });
        btnModify.addListener('click', FeatureList.modify);
        
        btnRemove = new Ext.Button({
                text:'Supprimer',
                tooltip:'Supprime la fonction selectionnée'
            });
        btnRemove.addListener('click', FeatureList.remove);
    
        this.mytree = new Ext.tree.TreePanel({
            width: 400,
            height: 320, 
            useArrows:true,
            autoScroll:true,
            animate:true,
            enableDD:true,//false,
            containerScroll: true,
            rootVisible:true,
            //singleExpand:false,

 /*           loader: new Ext.tree.TreeLoader({
    			preloadChildren: true,
    			clearOnLoad: true,
                dataUrl: '/feature/list/'
    		}),*/
            
            dataUrl: '/feature/list',

            root: {
                text: 'Fonctionnalités',
                id: 'tree_0' 
            }, 
            
            tbar:[btnAdd,'-',btnModify,'-',btnRemove]
        });
        
        this.mytree.on('click', function(node, e) {
           FeatureList.clickMenu(node); 
        });

        this.mytree.on('checkchange', function(node) {
            console.info(node);
            node.loadComplete();
            console.info(node);
            FeatureList.checkNode(node, $('cbx_' + node.attributes.num).checked);
        });
    },

    checkNode: function(node, checked) {
        $('cbx_' + node.attributes.num).checked = checked;
        // si je suis sur un noeud père
        if(!node.isLeaf()) {
            // si les noeuds enfants ont déjà été affichés
            if(node.childrenRendered){
                // on les coches tous
                node.childNodes.each(function(child) {
                    FeatureList.checkNode(child, checked);
                });
            }/* else {
                console.info(node);
            }*/
        }
    },
    
    render: function(element) {
        $(element).innerHTML = '';
        this.mytree.render(element);
        this.mytree.getRootNode().expand();
        this.resize();
    }, 
    
    clickMenu:function(arbre) {
        FeatureList.selectedFeature = arbre;
    },
    
    toogle:function(arbre) {
        if(arbre.expanded) {
            arbre.collapse();
        } else {
            arbre.expand();
        }
    },
    
    add: function() {

        var loading = new Loading(1);

        new Ajax.Request('/feature/add/',
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
    
    modify: function() {
        try {
            var loading = new Loading(1);

            new Ajax.Request('/feature/modify/id/' + FeatureList.selectedFeature.attributes.num, 
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
        } catch(e) {
            Ext.MessageBox.alert('Attention', 'vous devez selectionner la fonctionnalité a modifier');
        }
    },
    
    remove: function() {
        try{
            if(FeatureList.selectedFeature.leaf) {
                Ext.MessageBox.confirm(
                    'Confirmation', 
                    'Etes-vous sur de vouloir supprimer la fonctionnalité "' + FeatureList.selectedFeature.text + '" ?', 
                    function(btn) {
                        if(btn == 'yes') {
                            var loading = new Loading(1);

                            new Ajax.Request('/feature/remove/id/' + FeatureList.selectedFeature.attributes.num, 
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
                        }
                    }
                );
            } else {
                alert('vous ne pouvez pas supprimer une fonctionnalité qui contient d\'autres fonctionnalités');
            }
        } catch(e) {
            alert('vous devez selectionner une fonctionnalité a modifier');
        }
    },
    
    deleteEntry: function(id) {
        $$('li.x-tree-node div').each(function(node) {
            if(node.attributes[2].nodeValue == 'tree_'+id) {
                node.parentNode.remove();
                exit();
            }
        });
    },

    resize: function() {
        var new_height = screen.height - 470;
        
        if(new_height > this.mytree.getSize().height) {
            this.mytree.setHeight(new_height);
            $('rightMain').style.height = new_height;
        } else {
            $('rightMain').style.height = this.mytree.getSize().height;
        }
    }
};