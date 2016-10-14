<?php
namespace nigiri\exceptions;

/**
 * Represents an HTTP 500 error
 * @package site\exceptions
 */
class InternalServerError extends HttpException {
    public function __construct($str="", $detail="")
    {
        if(empty($str)){
            $str = 'Si è verificato un errore inaspettato';
        }
        $this->httpString = 'Internal Server Error';

        parent::__construct($str, 500, $detail);
    }
}
