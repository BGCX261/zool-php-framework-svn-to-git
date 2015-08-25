

var Yii = {};

/**
 * Connection function, returns connection Object
 */
Yii.Connection = function(config){
    
    _this = this;
    
    this.inited = []; // semafor
    this.inProgress = 0;
    
    this.config = {
        initUrl : '',
        appPath : Sys.file.getPathOf('AChrom')+ 'content' + DS + 'apps' + DS ,
        appRelativePath :  'apps/'
    };
    
    jQuery.extend(this.config, config);
    
    this.storeInitFile = function(file, url){
        
        Sys.debug('Yii downloads: '+ url);
        
        Sys.ajax({
            url : url,
            success: function(data){
                file = file.replace('/', DS); // must replace couse of WIN
                Sys.file.write(_this.config.appPath + file, data, 0755);
                _this.inited[_this.inProgress++]=true;                
            }
        });
        
    };
    
    this.tryOnReady = function(cb){

            inited = true;
            for(i in _this.inited){
                if(_this.inited[i] == false){
                    inited = false;
                    break;
                }
            }
            if(inited){
                setTimeout(cb, 1000);
            }else{ // try again
                setTimeout(function(){
                    _this.tryOnReady(cb);
                }, 200);
            }
        
    }
    
    return {
      
      /**
        * Initializes application with given config paramaters
        */
      init : function(callback){
       url = _this.config.initUrl;
        Sys.ajax({
           url : url,
           parse: 'json',
           success: function(data){
                _this.config.remote = data;                
                _this.config.appPath += Sys.file.normalizeDirName(_this.config.remote.appName) + DS;
                _this.config.appRelativePath += Sys.file.normalizeDirName(_this.config.remote.appName) + '/';
                
                                
                // init semafors setting
                num = data.files.length;
                _this.inited = new Array(num);
                for(i in _this.inited){
                    _this.inited[i]=false;
                }
                
                for(fileName in data.files){
                    fileUrl = data.files[fileName];
                    Sys.dump('Getting: '+ fileName + ' from: '+fileUrl);
                    _this.storeInitFile(fileName, fileUrl);   
                }
                
                
                _this.tryOnReady(callback);
            
           },
           error: function(event){
            window.alert(Sys.log(event,true));
           }
        });
        
      },
      
      startAppAndCloseWin : function(window) {
          var uri = _this.config.appRelativePath + _this.config.remote.mainWindow ;
           
          Sys.debug('Start app: ' + uri);
                    
          var ww = Sys.services.window();
          var win = ww.openWindow(null, uri, _this.config.appName, "chrome,centerscreen, resizable", null);
          window.close();
      },
      
      /**
       * Inits application and after starts it.
       */
      run : function(window){
        _yii = this;
        this.init(function(){
            if(window) _yii.startAppAndCloseWin(window); 
        });
      },

      config : _this.config      
        
    }; 
    
    
}


