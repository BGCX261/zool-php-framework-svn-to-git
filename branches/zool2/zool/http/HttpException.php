<?php

namespace zool\http;

use zool\exception\ZoolException;

/**
 *
 * @author Zsolt Lengyel
 *
 */
class HttpException extends ZoolException{

    /**
     * Code of response
     * @var int
     */
    private $statusCode;

    /**
     *
     * @param int $statusCode http status code
     */
    public function __construct($statusCode, $previous = null){
        $this->statusCode = $statusCode;
        parent::__construct('Http message', $statusCode, $previous);
    }

    public function getStatusCode(){
        return $this->statusCode;
    }



}