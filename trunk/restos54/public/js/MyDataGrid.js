var MyDataGrid = {

	grid: null,
	config: null,
	functions: $H(),
    gridId: null,

	render: function(config) {

        this.config = config;
        var parentNode = $(this.config.tableId).parentNode;
        this.grid = new Ext.grid.TableGrid(this.config.tableId, this.config.config);

        this.setGridId(parentNode);

        if(this.config.functions) {
			this.setFunctions(this.config.functions);
		}

        if(this.config.resize) {
            this.resize(this.config.resize);
        }

        if(this.config.buttons) {
            this.addButtons(this.config.buttons);
        }

        this.initEvent();

	},

    initEvent: function() {

    },

	action: function(fctName, params) {

		var qs = '';
		var parameters = '';
		if($H(params).size() > 0) {
			if($H(params).size() == 1) {
				qs = $H(params).toArray().first().key + '/' + $H(params).toArray().first().value;
			} else {
				parameters = $H(params).toQueryString();
			}
		}

		fct = this.getFunction(fctName);
		if(typeof(fct) != 'undefined') {
			if(fct.confirm) {
				Ext.MessageBox.confirm(
		            fct.confirm.title,
		            fct.confirm.msg,
		            function(btn) {
		                if(btn == 'yes') {
							MyDataGrid.sendAjax(fct.action + qs, parameters, fct.method);
						}
					}
				);
			} else {
				this.sendAjax(fct.action + qs, parameters, fct.method);
			}
		}
	},

	setFunctions: function(fcts) {
		$H(fcts).each(function(fct) {
			MyDataGrid.functions.set(fct.key, eval('fcts.'+fct.key));
		});
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

	getFunction: function(name) {
		return this.functions.get(name);
	},

    addButtons: function(buttons) {
        buttons.each(function(btn) {
            MyDataGrid.addButton(btn);
        });
    },

    addButton: function(btn) {
        var input = $(document.createElement('input'));
        input.value = btn.value;

        if(btn.typeName) {
            input.type = 'submit';
        } else {
            input.type = 'button';
        }
        if(btn.action) {
            Event.observe(input, 'click', function() {
                eval(btn.action);
            });
        }
        if(btn.className) {
            input.addClassName(btn.className);
        }

        var div = $(document.createElement('div'));
        div.addClassName('btnBottomGrid');

        div.appendChild(input);
        this.gridId.appendChild(div);
    },
    
    resize: function(resize) {
        
        // calcul de la meilleure taille optimale : 
        // on calcule la distance entre le haut de la grid et le conteneur "main"
        //var gridTop = this.grid.el.dom.offsetTop + this.grid.container.dom.up('table').offsetTop;
        var gridTop = this.grid.el.dom.offsetTop + $('tableMain').offsetTop;
        // on compte 25px par boutons
        var buttonsHeight = 0;
        if(typeof this.config.buttons != 'undefined') {
            buttonsHeight = this.config.buttons.size() * 25;
        }
        // on regarde la hauteur du conteneur "main"
        var mainHeight = $('main').offsetHeight;
        // maintenant on peut trouver la hauteur idéale.
        var idealHeight = mainHeight - gridTop - buttonsHeight;

        var newHeight = (resize.maxHeight < idealHeight ? resize.maxHeight : idealHeight);

        if(newHeight > this.grid.getSize().height) {
            this.grid.setHeight(newHeight);

            // taille de la partie de droite
            var rightHeight = this.grid.el.dom.offsetTop + newHeight  + buttonsHeight;
            $('rightMain').style.height = rightHeight;

        } else {
            $('rightMain').style.height = this.grid.getSize().height;
        }
        if(resize.rightHeight) {
            if(resize.rightHeight > this.grid.getSize().height) {
                $('rightMain').style.height = resize.rightHeight;
            }
        }

    },

    setGridId: function(parentNode) {
        this.gridId = $$(parentNode.tagName + '#' + parentNode.id + ' div.x-panel')[0];
    }
};
