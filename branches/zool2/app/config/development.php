<?php

return [

'zool'=>[

    'db'=> [
        'driver' => 'pdo_mysql',
        'host' =>'localhost',
        'user' => 'zool',
        'password' => 'zool',
        'dbname' => 'zool_zool'
     ],

    'log'=>[
        'path'=>'log/log'
     ],

     'request'=>[
         'viewIdParamName' => 'viewId'
     ]

],



];