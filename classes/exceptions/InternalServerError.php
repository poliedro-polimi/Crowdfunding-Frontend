<?php
namespace nigiri\exceptions;

/**
 * Represents an HTTP 500 error
 * @package site\exceptions
 */
class InternalServerError extends Exception {
    public function __construct($str="", $detail="")
    {
        if(empty($str)){
            $str = 'Si è verificato un errore inaspettato';
        }

        parent::__construct($str, 500, $detail);

        header('HTTP/1.0 500 Internal Server Error', true, 500);
    }
}
