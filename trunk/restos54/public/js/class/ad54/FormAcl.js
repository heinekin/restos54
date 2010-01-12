var FormAcl = {

    module: '', 
    controller: '',

    aclGrpElt: null,
    
    init: function(module, controller)
    {
        this.module = module;
        this.controller = controller;
        
        this.initElt();

        this.initEvent();
    },

    initElt: function()
    {
        this.aclGrpElt = $('accreditation_group_id');
    },

    initEvent: function()
    {
        Event.observe(this.aclGrpElt, 'change', FormAcl.onChangeAclGrp);
    },

    clearOptions: function()
    {
        var elt = $('fieldset-options');
        if(elt != null) {
            elt.remove();
        }
    },

    addOptions: function(html)
    {
        var rowSubmit = $('submitbutton').up('tr');
        var parentRow = rowSubmit.parentNode;
        
        var tr = document.createElement('tr');

        var td = document.createElement('td');
        td.setAttribute('colspan', 2);

        tr.appendChild(td);
        td.innerHTML = html;

        parentRow.insertBefore(tr, rowSubmit);
    },

    onChangeAclGrp: function(e)
    {
        if(FormAcl.aclGrpElt.getValue() > 0)
        {
            new Ajax.Request('/' + FormAcl.module + '/' + FormAcl.controller + '/listoption/',
            {
                method:'post',
                parameters: $H({'aclGrpId' : FormAcl.aclGrpElt.getValue()}).toQueryString(),
                onCreate: function() {
                    FormAcl.clearOptions();
                },
                onSuccess: function(req) {
                    if(req.responseText != '') {
                        FormAcl.addOptions(req.responseText);
                    }
                }
            });
        }
        else
        {
            FormAcl.clearOptions();
        }
    }
}