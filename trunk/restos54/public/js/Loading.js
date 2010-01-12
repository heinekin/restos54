function Loading(lang)
{
    this.msgElt = null;
    this.lang = lang;
    this.text = null;
    this.m = null;

    switch(lang) {
        case 1:
            this.text = 'Chargement...';
            break;
        case 2:
            this.text = 'Loading...';
            break;
        default:
            this.text = 'Chargement...';
            break;
    }

    if($('msg-div') == null)
    {
        Ext.DomHelper.insertFirst(document.body, {id:'msg-div'}, true).alignTo(document, 't-t');
        this.msgElt = $('msg-div');
    }
    else
    {
        this.msgElt = $('msg-div');
    }

    var box = ['<div class="x-box-tl"><div class="x-box-tr"><div class="x-box-tc"></div></div></div>',
                '<div class="x-box-ml"><div class="x-box-mr"><div class="x-box-mc"><h3>', this.text, '</h3></div></div></div>',
                '<div class="x-box-bl"><div class="x-box-br"><div class="x-box-bc"></div></div></div>'].join('');

    this.m = document.createElement('div');
    $(this.m).addClassName('msg');
    this.m.innerHTML = box;

    this.m = this.msgElt.appendChild(this.m);
    this.m.hide();

    this.start = function() {
        this.m.appear();
    }

    this.stop = function() {
        this.m.fade();

        var div = this.m;
        var fct = function() {
            div.remove();
        };
        setTimeout(fct, 2000);
    }

}
