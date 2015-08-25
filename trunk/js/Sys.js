/**
 * Other, used function
 */

 
String.prototype.repeat = function( num )
{
    return new Array( num + 1 ).join( this );
}

String.prototype.lastSplittedBy = function(separator){
    
    array = this.split(separator);
    return array[array.length-1];
    
}

String.prototype.getWithoutLast = function(separator){
    array = this.split(separator);
    var str = '';
    for(i=0; i<array.length-1; i++){
        str += array[i] + separator;
    }
    return str;
};

// Wrapper object
var Sys = {};

Sys.DEBUG = false;

// dump width break
Sys.dump = function(data){
	console.log(data);
};

/*
// Services factory
Sys.services = (function(){
    
    var ServiceFactory = function(){ 
         var services = {};  
    
        return {
          
          console : function(){
              return window.console;
          },
          
          request : function(){
            
          },
          
          directory : function(){
            services.directory = services.directory || Components.classes["@mozilla.org/file/directory_service;1"].  
                getService(Ci.nsIProperties);
            
            return services.directory;
           },
           
          localfile : function(){
            return Components.classes["@mozilla.org/file/local;1"].  
                createInstance(Ci.nsILocalFile);
          },
          
          fileOutputStream : function(){
            return Components.classes["@mozilla.org/network/file-output-stream;1"]
                 .createInstance( Ci.nsIFileOutputStream );
          },
          
          os : function(){
            services.os = services.os || Components.classes["@mozilla.org/xre/app-info;1"]  
               .getService(Ci.nsIXULRuntime).OS;
            
            return services.os; 
          },
          
          preferences : function(){
            services.preferences = services.preferences || Components.classes["@mozilla.org/preferences-service;1"]
                            .getService(Components.interfaces.nsIPrefBranch);
            
            return services.preferences;
          },
          
          runtime : function(){
            services.runtime = services.runtime || Components.classes["@mozilla.org/xre/app-info;1"]
                 .getService(Ci.nsIXULRuntime);
                 
            return services.runtime;
          },
          
          window : function(){
            services.window = services.window || Components.classes["@mozilla.org/embedcomp/window-watcher;1"]
                       .getService(Ci.nsIWindowWatcher);
            
            return services.window;
          },
            
        };
    };
    
    return new ServiceFactory(); 
    
})();

*/

/*
// Operating system detection
Sys.os = {};
Sys.os.isWindows = function(){ return ( Sys.services.os().indexOf('WINNT') > -1 ); };
Sys.os.isLinux =  function(){ return ( Sys.services.os().indexOf('Linux') > -1 ); };
Sys.os.isMac =  function(){ return ( Sys.services.os().indexOf('Darwin') > -1 ); };

// CONSTATNTS, hangs on services
var DS = Sys.os.isWindows ? '\\' : '/'; // Directory separator 
*/
// intelligent logger
// if isReturn is true, returns the merged string
Sys.log = function(obj, isReturn, depth){
    console.log(obj);
};

Sys.debug = function(string){    
    if(Sys.DEBUG){        
        console.log('[DEBUG] ' + string);
    }
    
}

// simle ajax query
Sys.ajax = function(conf){    
    var config = {
        url : "",
        error : function(){},
        success: function(){},
        method: 'GET',
        parse : 'json',        
    };
    
    jQuery.extend(config, conf);
    
    if(config.data ){
        config.url += "?"
        for(i in config.data){
        	var key = i;
        	var value = config.data[i];
        	
        	if(typeof value == 'object'){
        		
        		for(k in value){
        			config.url += key+'['+k+']=' + value[k] + '&';
        		}
        			
        	}else if(typeof value == 'function'){
        		v = value();
        		        		
        		if(typeof v == 'object'){
        			
            		for(k in v){            		
            			config.url += key+'['+k+']=' + v[k] + '&';
            		}
            			
            	}else{        	
            		config.url += key + "=" +v + "&";
            	}        		
        		
        		
        	}else{        	
        		config.url += key + "=" +value + "&";
        	}
         }
        
        delete config.data;
    }
    
    console.log(config);
    
   jQuery.ajax(config);
 
};
