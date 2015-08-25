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
  dump(data);
  dump('\n');  
};

const Cc = Components.classes;
const Ci = Components.interfaces;
const Cr = Components.results;
const Cu = Components.utils;

// Services factory
Sys.services = (function(){
    
    var ServiceFactory = function(){ 
         var services = {};  
    
        return {
          
          console : function(){
              services.console = services.console || Components.classes["@mozilla.org/consoleservice;1"]
                .getService(Ci.nsIConsoleService);
                
              return services.console;
          },
          
          request : function(){
            return Components.classes["@mozilla.org/xmlextras/xmlhttprequest;1"]
                  .createInstance(Ci.nsIXMLHttpRequest);
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

// Operating system detection
Sys.os = {};
Sys.os.isWindows = function(){ return ( Sys.services.os().indexOf('WINNT') > -1 ); };
Sys.os.isLinux =  function(){ return ( Sys.services.os().indexOf('Linux') > -1 ); };
Sys.os.isMac =  function(){ return ( Sys.services.os().indexOf('Darwin') > -1 ); };

// CONSTATNTS, hangs on services
var DS = Sys.os.isWindows ? '\\' : '/'; // Directory separator 

// intelligent logger
// if isReturn is true, returns the merged string
Sys.log = function(obj, isReturn, depth){
    return;
    var consoleService = Sys.services.console();
    
    var message = obj;
    var _depth = typeof depth == 'undefined' ? 1 : depth;;
    var length = obj ? obj.length : 0;
    
    if(typeof obj == 'object'){
        message = '[Object]';
        
        if(length === undefined){
            for(i in obj){
                if(obj[i]){
                    e = obj[i];
                    message += '\n'+ '  '.repeat(_depth) + i + ' : ' + Sys.log(e, true, _depth+1);
                }
            }
        }else{
            for(i=0;i<length;i++){
                if(obj[i]){
                    e = obj[i];
                    message += '\n'+ '  '.repeat(_depth) + i + ' : ' + Sys.log(e, true, _depth+1);
                }
            }    
        }
        
    }
    if(typeof obj == 'function'){
        message = '[Function]';        
    }
    if(typeof obj == 'string'){
        message = '[String:'+ obj.length +'] '+ obj;
    }
    
    if(isReturn) return message;
    
    consoleService.logStringMessage(message);

};

Sys.debug = function(string){    
    if(Sys.DEBUG){
        var console = Sys.services.console();
        console.logStringMessage('[DEBUG] ' + string);
    }
    
}

// simle ajax query
Sys.ajax = function(conf){
    
    var config = {
        url : "",
        data : null,
        error : function(){},
        success: function(){},
        method: 'GET',
        parse : false,        
    };
    
    jQuery.extend(config, conf);
   
   if(config.url == '') return;
    
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
    }
   Sys.log(config.url);
    request = Sys.services.request();
                  
    request.onload = function(event) {    
        var data = event.target.responseText;
        if(config.parse == 'json')
        	try{
        		var data = jQuery.parseJSON( data );       
        		
        	}catch(e){
        		data = {error : e.message};
        	}
        	config.success(data);

    };
    
    request.onerror = function(aEvent) {
       config.error(aEvent);
    };    
    
    request.open(config.method, config.url, true);
    request.send(null);
 
};

//
// File section
//
Sys.file = {};

Sys.file.normalizeDirName = function(dir){
    return dir.replace(/[^a-zA-Z0-9\._]/i, '_');
};

Sys.file.getPathOf = function(xulPath){
    return Sys.services.directory().get(xulPath, Components.interfaces.nsIFile).path + DS;
};

Sys.file.touch = function(path, chmod){
    var file = Sys.services.localfile();

    file.initWithPath(path);
    if( !file.exists() || !file.isFile() ) {   // if it doesn't exist, create
       try{
            file.create(Components.interfaces.nsIFile.FILE_TYPE, chmod ? chmod : 0777);
        }catch(e){
            Sys.dump(e);
        }
    }
    return file;
}

// Mkdir, if path is undefined, trys recursive create dir
Sys.file.mkdir = function(dir, path, chmod){
    
    Sys.debug('Try make dir: ' + dir + ', here : ' + path);
    
    var file = Sys.services.localfile();
    file.initWithPath(path);
    file.append(dir);    
    if(!file.isDirectory() ) {   // if it doesn't exist, create
       file.create(Components.interfaces.nsIFile.DIRECTORY_TYPE, chmod ? chmod : 0755);
    }
};

// file writing
Sys.file.write = function (path, data, chmod){

    Sys.debug('Try write file : ' + path);

    var file = Sys.services.localfile();

    file.initWithPath(path);
    if( !file.exists() || !file.isFile() ) {   // if it doesn't exist, create
       try{
            file.create(Components.interfaces.nsIFile.FILE_TYPE, chmod ? chmod : 0777);
        }catch(e){
            Sys.dump(e);
        }
    }
    
    var outputStream = Sys.services.fileOutputStream();
    outputStream.init( file, 0x04 | 0x08 | 0x20, 420, 0 );
    var result = outputStream.write( data, data.length );
    outputStream.close();
    
};


Sys.file.getFile = function(sPath) {
    try {
      var f = Cc["@mozilla.org/file/local;1"].createInstance(Ci.nsILocalFile);
      f.initWithPath(sPath);
      return f;
    } catch (e) {
      Cu.reportError('FileIO.getFile("' + sPath + '"): ' + e.message);
    }
    return null;
};

Sys.file.getFileFromProfDir = function(aAppendNames) {
    var file = Cc["@mozilla.org/file/directory_service;1"].getService(Ci.nsIProperties).get('ProfD', Ci.nsIFile);
    for each(let sName in aAppendNames)
      file.append(sName);
    return file;
};

Sys.file.read = function(file, charset) {
    // |file| is nsIFile
    var fstream = Cc["@mozilla.org/network/file-input-stream;1"].createInstance(Ci.nsIFileInputStream);
    var cstream = Cc["@mozilla.org/intl/converter-input-stream;1"].createInstance(Ci.nsIConverterInputStream);
    fstream.init(file, -1, 0, 0);
    cstream.init(fstream, charset, 0, 0);

    var data = "";
    var str = {};
    while (cstream.readString(4096, str) != 0) {
      data += str.value;
    }
    cstream.close();
    return data;
};

Sys.file.getLines = function(file, charset) {
    var istream = Cc["@mozilla.org/network/file-input-stream;1"].createInstance(Ci.nsIFileInputStream);
    istream.init(file, 0x01, 0444, 0);

    var is = Cc["@mozilla.org/intl/converter-input-stream;1"].createInstance(Ci.nsIConverterInputStream);

    //This assumes that istream is the nsIInputStream you want to read from
    is.init(istream, charset, 1024, 0xFFFD);

    // read lines into array
    var lines = [], line = {}, bHasMore = true;
    if (is instanceof Ci.nsIUnicharLineInputStream) {
      do {
          bHasMore = is.readLine(line);
          lines.push(line.value);
      } while (bHasMore);
    }
    istream.close();
    return lines;
};

//directory listing
Sys.file.dirListing = function(aDir, bRecursive, aExt) {
    var fileList = aDir.directoryEntries;

    var aSplit, sExt, msg = "";
    var file;
    var iFileCount = 0;
    var aFiles = [];
    while (fileList.hasMoreElements()) {
      file = fileList.getNext().QueryInterface(Ci.nsIFile);
      if (bRecursive) {
        if (file.isDirectory()) {
          var aTemp = this.dirListing(file, bRecursive, aExt);
          aFiles = aFiles.concat(aTemp);
        }
      }
      aSplit = file.leafName.split(".");
      sExt = aSplit[aSplit.length - 1]; 

      if (aExt == sExt.toLowerCase() || aExt == "*") {
        iFileCount++;
        aFiles.push([file.path, file.leafName, file.fileSize]);
      }
    }
    return aFiles;
 };

