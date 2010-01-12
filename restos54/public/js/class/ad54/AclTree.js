var AclTree = {

    tree: null,
    nodes: [],
    elt: null,
    childrenRendered: [], 

    classNode: 'grpWithoutAllAcl',

    init: function(nodes, elt) {

        this.nodes = nodes;
        this.elt = elt;

        this.initTree();
        this.initEvent();

        this.resize();
    },

    initTree: function() {

        // arbre des marques et des produits
        this.tree = new Ext.tree.TreePanel({
            renderTo: this.elt, 
            height: 300,
            useArrows:true,
            autoScroll:true,
            animate:true,
            enableDD:false,
            containerScroll: true,
            rootVisible:false,
            singleExpand:false,
            border: true,
            root: {
                text: 'root',
                id: 'root',
                children: this.nodes
            }
        });

    },

    initEvent: function() {

        this.tree.on('checkchange', function(node) {
            // si c'est un noeud parent
            if(!node.isLeaf()) {
                node.valueChanged = true;
            }

            AclTree.checkProductNodes(node);
        });

        this.tree.on('expandnode', function(node){
            // si le noeud a changé de valeur et que les enfants n'ont pas encore été chargé
            if(node.valueChanged && !node.childrenLoaded){
                AclTree.checkProductNodes(node);
            }
            node.childrenLoaded = true;
        });
    },

    checkProductNodes: function(node) {
        // si le noeud est un parent
        if(!node.isLeaf()) {
            // pour chaque noeuds enfants
            node.childNodes.each(function(noeud) {
                // si la case à cocher parente est cochée, je coche tous les noeuds enfant qui ne sont pas lockés
                if($(node.attributes.cbxId).checked) {
                    $(noeud.attributes.cbxId).checked = true;
                }
                // si la case à cocher parent est décochée, je décoche tous les noeuds enfant qui ne sont pas lockés
                else {
                    $(noeud.attributes.cbxId).checked = false;
                }
            });
            
            // si le noeud est décoché
            if(!$(node.attributes.cbxId).checked) {
                // on change le style de l'icon père
                this.removeIconProduct(node);
            }
        }
        // si c'est un enfant
        else {
            // si on est en train de le cocher
            if($(node.attributes.cbxId).checked) {
                // si tous les noeuds freres sont également cochés
                if(node.parentNode.childNodes.all(function(child){return $(child.attributes.cbxId).checked;})) {
                    // on coche le père
                    $(node.parentNode.attributes.cbxId).checked = true;
                    // on change le style de l'icon père
                    this.removeIconProduct(node.parentNode);
                }
            }
            // si on est en train de le décocher
            else {
                // s'il n'y a plus de noeuds freres encore cochés
                if(!node.parentNode.childNodes.any(function(child){return $(child.attributes.cbxId).checked;})) {
                    // on décoche le père
                    $(node.parentNode.attributes.cbxId).checked = false;
                    // on change le style de l'icon père
                    this.removeIconProduct(node.parentNode);
                }
                else
                {
                    // on change le style de l'icon père
                    this.addIconProduct(node.parentNode);
                }
            }
        }
    },

    addIconProduct: function(noeud) {
        if(!Prototype.Browser.IE) {
            noeud.ui.iconNode.addClassName(this.classNode);
        }
    },

    removeIconProduct: function(noeud) {
        if(!Prototype.Browser.IE) {
            noeud.ui.iconNode.removeClassName(this.classNode);
        }
    },

    resize: function() {
        // calcul de la meilleure taille optimale :
        // on calcule la distance entre le haut du Tree et le conteneur "rightmain"
        var treeTop = this.tree.el.dom.offsetTop + $('rightMain').offsetTop;
        // on compte 25px par boutons
        var buttonsHeight = 25;
        // on regarde la hauteur du conteneur "rightMain"
        var mainHeight = $('rightMain').offsetHeight;
        // maintenant on peut trouver la hauteur idéale.
        var idealHeight = mainHeight - treeTop - buttonsHeight;

        var newHeight = idealHeight;

        if(newHeight > this.tree.getSize().height) {
            this.tree.setHeight(newHeight);
        }
    }
}
