var MyLoader = {
    classAllowed: $A(['User', 'MediaManager','BlockVibes','SeasonValidator', 'FeatureList', 'FeatureRight', 'ProfileGrid', 'UrlAccredited', 'CacheDispo', 'Catalog', 'AclTree', 'OsPeriod', 'FormOs', 'FormAcl', 'Registration']),
    classPath: '/js/class/',

    require: function(fileName) {
        var script = document.createElement('script');
        script.src = fileName;
        $$('head')[0].appendChild(script);
        eval($(script).innerHTML);
    },
    
    load: function(className,project) {
        className = className.trim();
        if(!MyLoader.isLoaded(className)) {
            if(MyLoader.classAllowed.include(className) ) {
                MyLoader.require(MyLoader.classPath + project + '/' + className + '.js');
            }
        }
    },
    isLoaded: function(className) {
        flag = false;
        fileName = className + '.js';
        $$('script').each(function(script) {
            if(script.src.split('/').last() == fileName) {
                flag = true;
            }
        });
        return flag;
    }

}