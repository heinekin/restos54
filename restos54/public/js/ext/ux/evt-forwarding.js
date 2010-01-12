Ext.apply(Ext, {
    num: function(v, defaultValue){
        v = Number(Ext.isEmpty(v) || Ext.isBoolean(v) ? NaN : v);
        return isNaN(v) ? defaultValue : v;
    },
    isBoolean: function(v){
        return typeof v === 'boolean';
    }
});

Ext.onReady(function(){

    Ext.get('centregrid').forwardMouseEvents();

   

});
