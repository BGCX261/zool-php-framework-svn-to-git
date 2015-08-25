<?php

namespace zool\scope;

define('UNSPECIFIED_SCOPE', 0);
define('STATELESS_SCOPE', 0);
define('EVENT_SCOPE', 1);
define('SESSION_SCOPE', 2);
define('PAGE_SCOPE', 3);
define('REQUEST_SCOPE', 4);
define('APPLICATION_SCOPE', 5);
define('CONVERATION_SCOPE', 6);

/**
 * Enum class to constants.
 *
 * @author Zsolt Lengyel
 *
 */
class ScopeType
{

    const UNSPECIFIED = UNSPECIFIED_SCOPE;
    const STATELESS = STATELESS_SCOPE;
    const EVENT = EVENT_SCOPE;
    const SESSION = SESSION_SCOPE;
    const PAGE = PAGE_SCOPE;
    const REQUEST = REQUEST_SCOPE;
    const APPLICATION = APPLICATION_SCOPE;
    const CONVERSATION = CONVERATION_SCOPE;

}