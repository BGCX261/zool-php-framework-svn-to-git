<?php return array (
  'modules' => 
  array (
    'zool' => 
    array (
      'path' => 'C:\\servers\\wamp-php54\\www\\zool2\\zool',
      'info' => 
      array (
        'name' => 'Zool framework',
        'version' => '0.1b',
        'authors' => 
        array (
          0 => 
          array (
            'author' => 'Zsolt Lengyel, ',
            'email' => 'zsolt.lengyel.it@gmail.com',
          ),
        ),
        'description' => 'Lorem ipsum',
      ),
    ),
    'app' => 
    array (
      'path' => 'C:\\servers\\wamp-php54\\www\\zool2\\app',
      'info' => 
      array (
        'name' => 'Zool application',
      ),
    ),
    'basetag' => 
    array (
      'path' => 'C:\\servers\\wamp-php54\\www\\zool2\\zool\\module\\basetag',
      'info' => 
      array (
      ),
    ),
    'html' => 
    array (
      'path' => 'C:\\servers\\wamp-php54\\www\\zool2\\zool\\module\\html',
      'info' => 
      array (
        'name' => 'HTML view implementation',
        'version' => '0.1b',
      ),
    ),
    'first' => 
    array (
      'path' => 'C:\\servers\\wamp-php54\\www\\zool2\\app\\module\\first',
      'info' => 
      array (
      ),
    ),
  ),
  'models' => 
  array (
    0 => 'app\\model\\Bug',
    1 => 'app\\model\\User',
    2 => 'first\\model\\cmp\\Model',
  ),
  'components' => 
  array (
    'zool.applicationFactory' => 
    array (
      'class' => 'zool\\component\\ApplicationFactory',
      'annotations' => 
      array (
        0 => 
        array (
          'zool\\annotation\\Component' => 
          array (
            'value' => 'zool.applicationFactory',
          ),
        ),
        1 => 
        array (
          'zool\\annotation\\Scope' => 
          array (
            'value' => 0,
          ),
        ),
      ),
      'properties' => 
      array (
      ),
      'methods' => 
      array (
      ),
    ),
    'zool.database.databaseController' => 
    array (
      'class' => 'zool\\component\\database\\DatabaseController',
      'annotations' => 
      array (
        0 => 
        array (
          'zool\\annotation\\Component' => 
          array (
            'value' => 'zool.database.databaseController',
          ),
        ),
      ),
      'properties' => 
      array (
        'entityManager' => 
        array (
          'annotations' => 
          array (
            0 => 
            array (
              'zool\\annotation\\In' => 
              array (
                'required' => true,
                'value' => 'zool.database.entityManager',
              ),
            ),
          ),
        ),
        'schemaTool' => 
        array (
          'annotations' => 
          array (
            0 => 
            array (
              'zool\\annotation\\In' => 
              array (
                'required' => true,
                'value' => 'zool.database.schemaTool',
              ),
            ),
          ),
        ),
      ),
      'methods' => 
      array (
      ),
    ),
    'zool.databaseFactory' => 
    array (
      'class' => 'zool\\component\\database\\DatabaseFactory',
      'annotations' => 
      array (
        0 => 
        array (
          'zool\\annotation\\Component' => 
          array (
            'value' => 'zool.databaseFactory',
          ),
        ),
      ),
      'properties' => 
      array (
      ),
      'methods' => 
      array (
      ),
    ),
    'zool.http.httpFactory' => 
    array (
      'class' => 'zool\\component\\http\\HttpFactory',
      'annotations' => 
      array (
        0 => 
        array (
          'zool\\annotation\\Component' => 
          array (
            'value' => 'zool.http.httpFactory',
          ),
        ),
      ),
      'properties' => 
      array (
      ),
      'methods' => 
      array (
      ),
    ),
    'zool.i18n.localizationFactory' => 
    array (
      'class' => 'zool\\component\\i18n\\LocalizationFactory',
      'annotations' => 
      array (
        0 => 
        array (
          'zool\\annotation\\Component' => 
          array (
            'value' => 'zool.i18n.localizationFactory',
          ),
        ),
      ),
      'properties' => 
      array (
      ),
      'methods' => 
      array (
      ),
    ),
    'zool.resource.resources' => 
    array (
      'class' => 'zool\\component\\resource\\Resources',
      'annotations' => 
      array (
        0 => 
        array (
          'zool\\annotation\\Component' => 
          array (
            'value' => 'zool.resource.resources',
          ),
        ),
      ),
      'properties' => 
      array (
      ),
      'methods' => 
      array (
      ),
    ),
    'firstController1' => 
    array (
      'class' => 'app\\component\\controller\\FirstController',
      'annotations' => 
      array (
        0 => 
        array (
          'zool\\annotation\\Component' => 
          array (
            'value' => 'firstController1',
          ),
        ),
        1 => 
        array (
          'zool\\annotation\\Scope' => 
          array (
            'value' => 1,
          ),
        ),
      ),
      'properties' => 
      array (
        'staticProp' => 
        array (
          'annotations' => 
          array (
            0 => 
            array (
              'zool\\annotation\\In' => 
              array (
                'required' => false,
                'value' => 'resources',
              ),
            ),
          ),
        ),
        'var' => 
        array (
          'annotations' => 
          array (
            0 => 
            array (
              'zool\\annotation\\In' => 
              array (
                'required' => false,
                'value' => NULL,
              ),
            ),
            1 => 
            array (
              'zool\\annotation\\Out' => 
              array (
                'required' => false,
                'scope' => 2,
                'value' => NULL,
              ),
            ),
          ),
        ),
        'log' => 
        array (
          'annotations' => 
          array (
            0 => 
            array (
              'zool\\annotation\\Logger' => 
              array (
                'value' => 'paramLogger',
              ),
            ),
          ),
        ),
        'log3' => 
        array (
          'annotations' => 
          array (
            0 => 
            array (
              'zool\\annotation\\Logger' => 
              array (
                'value' => NULL,
              ),
            ),
          ),
        ),
        'id' => 
        array (
          'annotations' => 
          array (
            0 => 
            array (
              'zool\\annotation\\RequestParam' => 
              array (
                'required' => true,
                'value' => NULL,
              ),
            ),
          ),
        ),
        'es' => 
        array (
          'annotations' => 
          array (
            0 => 
            array (
              'zool\\annotation\\In' => 
              array (
                'required' => false,
                'value' => 'first.es',
              ),
            ),
          ),
        ),
      ),
      'methods' => 
      array (
        'run' => 
        array (
          'annotations' => 
          array (
            0 => 
            array (
              'zool\\annotation\\RequestParameterized' => 
              array (
                'required' => false,
                'value' => NULL,
              ),
            ),
          ),
        ),
      ),
    ),
    'app.factory' => 
    array (
      'class' => 'app\\component\\data\\Factory',
      'annotations' => 
      array (
        0 => 
        array (
          'zool\\annotation\\Component' => 
          array (
            'value' => 'app.factory',
          ),
        ),
      ),
      'properties' => 
      array (
      ),
      'methods' => 
      array (
      ),
    ),
    'first.es' => 
    array (
      'class' => 'first\\component\\Es',
      'annotations' => 
      array (
        0 => 
        array (
          'zool\\annotation\\Component' => 
          array (
            'value' => 'first.es',
          ),
        ),
      ),
      'properties' => 
      array (
      ),
      'methods' => 
      array (
      ),
    ),
  ),
  'factories' => 
  array (
    'zool.application' => 
    array (
      'component' => 'zool.applicationFactory',
      'method' => 'application',
      'static' => false,
      'scope' => 0,
    ),
    'zool.events' => 
    array (
      'component' => 'zool.applicationFactory',
      'method' => 'events',
      'static' => false,
      'scope' => 0,
    ),
    'zool.util.time.watch' => 
    array (
      'component' => 'zool.applicationFactory',
      'method' => 'watch',
      'static' => false,
      'scope' => 0,
    ),
    'zool.database.entityManager' => 
    array (
      'component' => 'zool.databaseFactory',
      'method' => 'entityManager',
      'static' => false,
      'scope' => 0,
    ),
    'zool.database.schemaTool' => 
    array (
      'component' => 'zool.databaseFactory',
      'method' => 'schemaTool',
      'static' => false,
      'scope' => 0,
    ),
    'zool.http.request' => 
    array (
      'component' => 'zool.http.httpFactory',
      'method' => 'request',
      'static' => false,
      'scope' => 0,
    ),
    'zool.http.session' => 
    array (
      'component' => 'zool.http.httpFactory',
      'method' => 'session',
      'static' => false,
      'scope' => 0,
    ),
    'zool.http.cookie' => 
    array (
      'component' => 'zool.http.httpFactory',
      'method' => 'cookie',
      'static' => false,
      'scope' => 0,
    ),
    'zool.i18n.messages' => 
    array (
      'component' => 'zool.i18n.localizationFactory',
      'method' => 'messages',
      'static' => false,
      'scope' => 0,
    ),
    'zool.i18n.messageFormat' => 
    array (
      'component' => 'zool.i18n.localizationFactory',
      'method' => 'messageFormat',
      'static' => false,
      'scope' => 0,
    ),
    'hashVal' => 
    array (
      'component' => 'firstController1',
      'method' => 'hash',
      'static' => false,
      'scope' => 0,
    ),
    'app.data' => 
    array (
      'component' => 'app.factory',
      'method' => 'data',
      'static' => false,
      'scope' => 0,
    ),
  ),
  'events' => 
  array (
    'valami' => 
    array (
      0 => 
      array (
        'component' => 'firstController1',
        'method' => 'run',
        'static' => false,
        'priority' => 1,
      ),
    ),
  ),
);
