var ProfileGrid = {
    
    editCode: function(elt) {
        var spanElt = elt;
        var idElt = elt.id.split('_')[1];
        var parentElt = spanElt.parentNode;

        newVal = document.createElement('input');
        newVal.value = spanElt.innerHTML;
        newVal.type = 'text';
        newVal.name = 'newVal_' + idElt;
        
        //spanElt.style.removeProperty('color');
        spanElt.hide();
        
        parentElt.appendChild(newVal);
        Event.observe(newVal, 'blur', ProfileGrid.saveCode);
        newVal.focus();
    },
    
    saveCode: function(e) {

        var newVal = e.target;
        var idElt = newVal.name.split('_')[1];
        var spanElt = $('spanProfile_'+idElt);
        var parentElt = newVal.parentNode;
        
        newVal.disabled = true;
        if( (newVal.value != spanElt.innerHTML) && (newVal.value.strip() != '')){
            var loading = new Loading(1);
            new Ajax.Request('/profile/modify/', 
            {
                method:'post', 
                parameters: $H({'code': newVal.value, 'id': idElt}).toQueryString(),
                onCreate: function() {
                    loading.start();
                },
                onSuccess: function(req) {
                    loading.stop();
                    eval('json = ' + req.responseJSON);
                    if(json.state == 'ok') {
                        spanElt = $('spanProfile_'+json.id);
                        parentElt = spanElt.parentNode;
                        inputElt = parentElt.getElementsByTagName('input')[0];
                        
                        spanElt.innerHTML = inputElt.value;
                        parentElt.removeChild(inputElt);
                        spanElt.show();
                    } else {
                        spanElt = $('spanProfile_'+json.id);
                        parentElt = spanElt.parentNode;
                        inputElt = parentElt.getElementsByTagName('input')[0];
                        
                        parentElt.removeChild(inputElt);
                        spanElt.setStyle({color: '#F00'});
                        spanElt.show();
                    }
                } 
                
            });
        } else {
            parentElt.removeChild(newVal);
            spanElt.show();
        }
    }
};
