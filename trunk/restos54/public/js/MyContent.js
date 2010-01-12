var MyContent = {
    
    
    
    reset: function() {
        $('main').innerHTML = '';
    },
    
    loading: function() {
        //console.info('MyContent.loading');
        $('main').innerHTML = '<p>Chargement...</p>';
    },
    
    load: function(value) {
        $('main').innerHTML = value;
    }
    
}