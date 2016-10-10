<?php
/**
 * Classe di appoggio usata per rappresentare oggetti di modelli inesistenti ma di cui esistono tabelle nel DB
 * Oggetti di questa classe sono in modalità sola-lettura. Non è quindi possibile scrivere sul DB
 * Per scrivere sul DB definire un model vero e proprio per questa tabella
 */
class GenericModel{
  private $table;
  private $attr_data;

  static private $nextTable;

  public function __construct($table, $data=array()){
    $this->table=$table;
    if(!empty($data)){
      foreach($data as $k=>$v){
        $this->attr_data[$this->normalizeAttributeName($k)]=$v;
      }
    }
    else{
      $this->attr_data=array();
    }
  }

  public function buildMany(DbResult $data){
    if(!empty(self::$nextTable)){
      $out=array();
      while($row=$data->fetch()){
        $out[]=new GenericModel(self::$nextTable,$row);
      }
      return $out;
    }
    throw new ModelException("Impossibile generare oggetti Generici, le informazioni di base delle tabelle non sono impostate.");
  }

  public static function setNextTableType($table){
    self::$nextTable=$table;
  }

  public function getTableName(){
    return $this->table;
  }

  /**
   * Implementa getter e setter generici per tutti gli attributi
   */
  public function __call($name, $arguments){
    $method=substr($name, 0,3);
    if($method=='get' || $method=='set'){
      $attr_name=substr($name,3);
      if(!empty($attr_name)){
        if($method=='get'){
          return $this->getAttribute($attr_name);
        }
        elseif($method=='set'){
          return $this->setAttribute($attr_name, $arguments[0]);
        }
      }
    }
    throw new ModelException("Il metodo richiesto non esiste in questo Model",-1);
  }

  public function getAttribute($name){
    $name=self::normalizeAttributeName($name);
    if(array_key_exists($name,$this->attr_data)){
      return $this->attr_data[$name];
    }
    throw new ModelException("L'attributo richiesto non esiste.");
  }

  public function hasAttribute($name){
    $name=self::normalizeAttributeName($name);
    return array_key_exists($name,$this->attr_data);
  }

  private function setAttribute($name, $value=null){
    if($value===null && is_array($name)){
      foreach($name as $k=>$v){
        $this->setAttribute($k,$v);
      }
    }
    else{
      $this->attr_data[$this->normalizeAttributeName($name)]=$value;
    }
  }

  private static function normalizeAttributeName($name){
    return str_replace('_', '', strtolower($name));
  }
}
