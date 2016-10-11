<?php
namespace nigiri\exceptions;

/**
 * Represents an HTTP 400 error
 * @package site\exceptions
 */
class BadRequest extends Exception {
    public function __construct($str="", $detail="")
    {
        if(empty($str)){
            $str = 'I dati inviati sono incorretti';
        }

        parent::__construct($str, 400, $detail);

        header('HTTP/1.0 400 Bad Request', true, 400);
    }
}
