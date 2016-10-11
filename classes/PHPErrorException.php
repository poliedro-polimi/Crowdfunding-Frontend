<?php
namespace nigiri;

/**
 * Handles a
 * Class PHPErrorException
 */
class PHPErrorException extends Exception {

  private $context;

  public function __construct($errno, $errstr, $errfile=null, $errline=null, $errcontext=null){
    parent::__construct("Si è verificato un errore interno!", $errno, $errstr);
    $this->file=$errfile;
    $this->line=$errline;
    $this->context=$errcontext;
  }

  public function renderFullError(){
    return $this->translateLevel().': '.parent::getInternalError().' in '.$this->file." at line ".$this->line.".";
  }

  public function getContext(){
    return $this->context;
  }

  public function getContextAsString(){
    $out='';
    if(!empty($this->context)) {
      ob_start();
      var_dump($this->context);
      $out .= ob_get_contents();
      ob_end_clean();
    }
    return $out;
  }

  protected function renderEmailText(){
    return parent::renderEmailText()."

    Contesto:
    ".$this->getContextAsString();
  }

  private function translateLevel(){
    switch($this->getCode()){
      case E_ERROR:
      case E_USER_ERROR:
        return 'Errore Fatale';
      case E_WARNING:
      case E_USER_WARNING:
        return 'Attezione';
      case E_NOTICE:
      case E_USER_NOTICE:
        return 'Avviso';
      case E_STRICT:
        return 'Strict';
      case E_DEPRECATED:
      case E_USER_DEPRECATED:
        return 'Deprecated';
    }
  }

  public static function isFatal($level){
    return in_array($level, array(E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR,
      E_COMPILE_WARNING));
  }
}
