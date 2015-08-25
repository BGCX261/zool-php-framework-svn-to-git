<?php

namespace zool\component\http;

use zool\http\HttpCookie;

use zool\http\Request;

use zool\http\Session;

use zool\component\Component;

/**
 *
 * @author Zsolt Lengyel
 *
 * @Component("zool.http.httpFactory")
 */
class HttpFactory extends Component{

    /** @Factory("zool.http.request") */
    public function request(){
        return Request::instance();
    }

    /** @Factory("zool.http.session") */
    public function session(){
        return Session::instance();
    }

    /** @Factory("zool.http.cookie") */
    public function cookie(){
        return HttpCookie::instance();
    }

}