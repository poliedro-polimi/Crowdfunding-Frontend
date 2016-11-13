<?php
namespace nigiri\exceptions;

/**
 * An exception representing a generic HTTP error
 * @package nigiri\exceptions
 */
class HttpException extends Exception{
    protected $httpString;

    public function __construct($str, $no, $detail)
    {
        parent::__construct($str, $no, $detail);
    }

    public function unCaughtEffect()
    {
        header('HTTP/1.0 '.$this->getCode().' '.$this->httpString, true, $this->getCode());
    }


}
