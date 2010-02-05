var FeatureRight = {
    mytree: null,
    selectedFeature: 0,
    selected_node: null,
    profileId: null,

    init: function(profileId) {

        FeatureRight.profileId = profileId;

        this.btnSaveRights = new Ext.Button({
                text:'Sauvegarder',
                tooltip:'Sauvegarder',
                handler: function() {
                    FeatureRight.updateRights();
                    this.disable();
                },
                disabled: 'true'

        });

        this.what = new Ext.Button ({
            text: 'what',
            handler : function() {
                alert(  "ID: " + FeatureRight.findSelectedNode().attributes.id + "\r\n" +
                        "Text: "+ FeatureRight.findSelectedNode().attributes.text + "\r\n" +
                        "Recursive: " + FeatureRight.findSelectedNode().attributes.isRecursive + "\r\n" +
                        "clicState: " + FeatureRight.findSelectedNode().attributes.clicState);
            }
        });

        this.toolBar = new Ext.Toolbar({
           items: [this.btnSaveRights]
        });

        this.mytree = new Ext.tree.TreePanel({
            width: 400,
            height: 280,
            useArrows:true,
            autoScroll:true,
            animate:true,
            enableDD: false,
            containerScroll: true,
            rootVisible:true,
            dataUrl: '/ad54/profile/list/id/' + profileId ,
            root: {
                text: 'Fonctionnalités',
                id: 'tree_0'
            },
            tbar: this.toolBar
        });

        // recursive check
        this.mytree.on('checkchange', function(node) {
            FeatureRight.btnSaveRights.enable();
            node.attributes.clicState++;
            // a leaf has only 2 states
            if (node.isLeaf()) node.attributes.clicState++;
            if (node.attributes.clicState>0) {
                $('cbx_' + node.attributes.num).checked = true;
            }
            if (node.attributes.clicState==2) {
                node.attributes.isRecursive = true;                             // set the checked node to recursive
                FeatureRight.setRecursiveNode(node, true);                      // and so it its children
                if (!node.isLeaf()) FeatureRight.checkNode(node, true);         // keep the checkbox checked
            }
            if ( node.attributes.clicState > 2)  {                              // and reset everything after one loop
                node.attributes.clicState = 0 ;
                $('cbx_' + node.attributes.num).checked = false;
                node.ui.iconNode.removeClassName('replicatedFeatureRights');
                node.attributes.isRecursive = false;
                FeatureRight.checkNode(node, false);
                // we unchecked an item so it's parent don't have to recurse again...
                FeatureRight.recurseParentMode(node, false);
            }
            // check also if the parent has to be recursive
            if (FeatureRight.isTeamRecursive(node)) FeatureRight.recurseParentMode(node,true);
            if (node.attributes.clicState>0) FeatureRight.checkParent(node);
        });

        this.mytree.on('expandnode', function(node){                            // si on étend alors
                node.childNodes.each(function(noeud) {                              // on prend chaque noeud enfant
                    FeatureRight.setRecursiveNode(noeud, 
                        (node.attributes.isRecursive && node!=FeatureRight.mytree.getRootNode())
                        || noeud.attributes.isRecursive
                    );
                    
                    if (node.attributes.isRecursive && node.attributes.clicState>0) {
                        FeatureRight.checkNode(node, true);
                    }
                    if (node.attributes.clicState==0) {
                        FeatureRight.checkNode(node, false);
                    }
                    
                });
        });
    },

    checkParent: function (noeud) {
        // if a child is checked then check also all recursive Parent except the root
        if (noeud.parentNode!=FeatureRight.mytree.getRootNode()) {
            $('cbx_' + noeud.parentNode.attributes.num).checked = true;
            if (noeud.parentNode.attributes.isRecursive) noeud.parentNode.attributes.clicState = 2;
                else noeud.parentNode.attributes.clicState = 1;
            FeatureRight.checkParent(noeud.parentNode);
        }
    },

    recurseParentMode: function (noeud,mode) {
        if (noeud.parentNode!=FeatureRight.mytree.getRootNode()) {
            FeatureRight.setRecursiveNode(noeud.parentNode,mode);
            if (mode) noeud.parentNode.attributes.clicState=2; else noeud.parentNode.attributes.clicState=1;
            if (FeatureRight.isTeamRecursive(noeud.parentNode)==mode) FeatureRight.recurseParentMode(noeud.parentNode, mode);
        }
    },

    setRecursiveNode: function(noeud, mode) {
        if (!noeud.isLeaf()) {
            noeud.attributes.isRecursive=mode;
            if (mode) {
                if(!Prototype.Browser.IE) noeud.ui.iconNode.addClassName('replicatedFeatureRights');
                noeud.attributes.clicState=2;
            } else {
                if(!Prototype.Browser.IE) noeud.ui.iconNode.removeClassName('replicatedFeatureRights');    
                if (noeud.attributes.clicState>1) noeud.attributes.clicState--;
            }
        } else {
            // is't a leaf ?
            if (mode) noeud.attributes.clicState=2;
            noeud.attributes.isRecursive=false;
        }
    },

    isTeamRecursive: function (node) {
        var rmode = true;
        node.parentNode.childNodes.each(function(team) {
            if (!team.attributes.isRecursive) rmode = false;
        });
        return rmode;
    },

    findSelectedNode: function(node) {
        if (!node) node = FeatureRight.mytree.getRootNode();
        node.childNodes.each(function(noeud) {
            if (noeud.isSelected()) {
                FeatureRight.selected_node = noeud;
            }
            if (!noeud.isLeaf()) {
                FeatureRight.findSelectedNode(noeud);
            }
        });
        return FeatureRight.selected_node;
    },


    checkNode: function(node, checked) {
        $('cbx_' + node.attributes.num).checked = checked;
        //FeatureRight.updateRights(1,1);
        // si je suis sur un noeud père
        if(!node.isLeaf()) {
            // on les (dé)coches tous
            node.childNodes.each(function(child) {
                child.attributes.clicState = node.attributes.clicState;
                FeatureRight.setRecursiveNode(child,node.attributes.isRecursive );
                FeatureRight.checkNode(child, checked);
            });
        } else {
             //if (checked) node.attributes.clicState=2; else node.attributes.clicState=0;
             node.attributes.isRecursive=false;
        }
    },



    sendAjax: function(action, parameters, method) {
	var loading = new Loading(1);
        new Ajax.Request(action,
	        {
	            parameters: parameters,
				method: method,
                onCreate: function() {
                    loading.start();
                },
	            onSuccess: function(req) {
                    loading.stop();
	                xmlResponse(req.responseXML);
	            }
	        });
	},


    updateRights: function() {
        var features = '';
        var del_features = '';
        $$('input').each(function(e){
            if(e.id != "_ilx_alertShown_" && e.id.substr(0,3)!="ext" && e.value != "Ajouter" ){
            if(e.type == 'checkbox' && e.checked) {
                if (!FeatureRight.mytree.getNodeById('tree_' + e.value).parentNode.attributes.isRecursive) features+="{\"id\": \"" + e.value + "\", \"recursive\": " + FeatureRight.mytree.getNodeById('tree_' + e.value).attributes.isRecursive + "},";
            } else if(e.type == 'checkbox'){
                del_features+="{\"id\": \"" + e.value + "\"},";
            }}
        });
        FeatureRight.sendAjax('/ad54/profile/update', {features: features, del_features: del_features, profile_id : FeatureRight.profileId}, 'post');
    },

    render: function(element) {
        $(element).innerHTML = '';
        this.mytree.render(element);
        this.mytree.getRootNode().expand();
        this.resize();
    },

    clickMenu:function(arbre) {
        FeatureRight.selectedFeature = arbre;
    },

    toogle:function(arbre) {
        if(arbre.expanded) {
            arbre.collapse();
        } else {
            arbre.expand();
        }
    },

    resize: function() {
        var new_height = screen.height - 500;

        if(new_height > this.mytree.getSize().height) {
            this.mytree.setHeight(new_height);
            $('rightMain').style.height = new_height;
        } else {
            $('rightMain').style.height = this.mytree.getSize().height;
        }
    }

};