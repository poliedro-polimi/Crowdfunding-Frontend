<?php
namespace nigiri\exceptions;

/**
 * Represents an HTTP 404 error
 * @package site\exceptions
 */
class FileNotFound extends HttpException {
    public function __construct($str="", $detail="")
    {
        $this->theme = ':'.dirname(__DIR__).'/views/http404.php';

        if(empty($str)){
            $str = 'La pagina richiesta non esiste';
        }
        $this->httpString = 'Not Found';

        parent::__construct($str, 404, $detail);
    }
}
