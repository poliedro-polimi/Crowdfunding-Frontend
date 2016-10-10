<?php
namespace site\exceptions;

use site\Exception;

/**
 * Represents an HTTP 404 error
 * @package site\exceptions
 */
class FileNotFound extends Exception {
    public function __construct($str="", $detail="")
    {
        if(empty($str)){
            $str = 'La pagina richiesta non esiste';
        }

        parent::__construct($str, 404, $detail);
    }
}
