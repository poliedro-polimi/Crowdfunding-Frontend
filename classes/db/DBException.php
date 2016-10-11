<?php
namespace nigiri\db;

use nigiri\exceptions\Exception;

/**
 * Exception class for all db operations
 */
class DBException extends Exception  {

    private $query;
    /**
     * @param $mess: the message describing the error
     * @param $query: the query that generated the error
     * @param $n: identification number for the error
     */
    public function __construct($mess,$query='',$n=0){
        parent::__construct("Errore inaspettato nella richiesta al database!",$n, $mess);
        $this->query=$query;
    }

    /**
     * Returns the query that triggered the DB error
     * @return string
     */
    public function getQuery(){
        return $this->query;
    }

    public function renderFullError(){
        return parent::renderFullError()." [Query: ".$this->getQuery()."]";
    }
}
