<?php
namespace nigiri\exceptions;

/**
 * Represents an HTTP 400 error
 * @package site\exceptions
 */
class BadRequest extends HttpException {
    public function __construct($str="", $detail="")
    {
        if(empty($str)){
            $str = 'I dati inviati sono incorretti';
        }
        $this->httpString = 'Bad Request';

        parent::__construct($str, 400, $detail);
    }
}
