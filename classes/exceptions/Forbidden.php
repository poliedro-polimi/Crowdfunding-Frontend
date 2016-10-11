<?php
namespace nigiri\exceptions;

use nigiri\Exception;

/**
 * Represents an HTTP 403 error
 * @package site\exceptions
 */
class Forbidden extends Exception {
    public function __construct($str="", $detail="")
    {
        if(empty($str)){
            $str = 'Non hai i permessi per accedere a questa pagina';
        }

        parent::__construct($str, 403, $detail);

        header('HTTP/1.0 403 Forbidden', true, 403);
    }
}
