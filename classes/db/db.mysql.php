<?php

class MySqlDriver implements DbInterface{
  private $connection;
  private $transactionTime=false;

  /**
   * @inheritdoc
   */
  public function __construct($data){
    if(empty($data['host']) or empty($data['user']) or empty($data['dbname'])){
      throw new DBException("Parametri di connessione al Database insufficienti!");
    }

    if(empty($data['password'])){
      $data['password']='';
    }
    if(empty($data['port'])){
      $data['port']=null;
    }
    if(empty($data['socket'])){
      $data['socket']=null;
    }

    $this->connection=new mysqli($data['host'],$data['user'],$data['password'],$data['dbname'],$data['port'],$data['socket']);

    if($this->connection->connect_error){
      throw new DBException("Errore di connessione al server MySql: ".$this->connection->connect_error, '', $this->connection->connect_errno);
    }

    if(!$this->connection->set_charset("utf8")) {
      throw new DBException("Impossibile impostare il set di caratteri UTF-8 per il database");
    }
  }

  /**
   * @inheritdoc
   */
  public function query($sql,$oneshot=false,$mode=RESULT_ASSOC){
    $result=$this->connection->query($sql);
    if(!$result){
      throw new DBException("Query Fallita: ".$this->connection->error,$sql,$this->connection->errno);
    }

    if($result===true){//Non-SELECT queries
      return true;
    }

    $resObj=new MySqlResult($result);
    if($oneshot){
      return $resObj->fetch($mode);
    }
    else{
      return $resObj;
    }
  }

  /**
   * @inheritdoc
   */
  public function startTransaction($options = null){
    if(!empty($options['isolation'])){
      $this->connection->query("SET TRANSACTION ISOLATION LEVEL ".$options['isolation']);
    }
    $this->connection->query("START TRANSACTION ".((!empty($options['isolation']) and
                               $options['isolation']=='REPEATABLE READ')?'WITH CONSISTENT SNAPSHOT':''));
    $this->connection->autocommit(false);
    $this->transactionTime=true;
  }

  /**
   * @inheritdoc
   */
  public function commitTransaction(){
    $this->connection->commit();
    $this->connection->autocommit(true);
    $this->transactionTime=false;
  }

  /**
   * @inheritdoc
   */
  public function rollbackTransaction(){
    $this->connection->rollback();
    $this->connection->autocommit(true);
    $this->transactionTime=false;
  }

  /**
   * @inheritdoc
   */
  public function isTransactionActive(){
    return $this->transactionTime;
  }

  /**
   * @inheritdoc
   */
  public function escape($data){
    return $this->connection->real_escape_string($data);
  }

  /**
   * @inheritdoc
   */
  public function getLastError($numeric=false){
    if($numeric){
      return $this->connection->errno;
    }
    return $this->connection->error;
  }

  /**
   * @inheritdoc
   */
  public function getLastAffectedRows(){
    return $this->connection->affected_rows;
  }

  /**
   * @inheritdoc
   */
  public function getLastInsertId(){
    return $this->connection->insert_id;
  }

  /**
   * @inheritdoc
   */
  public function close(){
    $this->connection->close();
  }

  public function __destruct(){
    $this->close();
  }
}


class MySqlResult implements DbResult{
  private $result;

  public function __construct(mysqli_result $result){
    if($result instanceof mysqli_result){
      $this->result=$result;
    }
    else{
      throw new DBException("Invalid result resource type!");
    }
  }

  /**
   * @inheritdoc
   */
  public function fetch($mode=RESULT_ASSOC){
    switch($mode){
      case RESULT_ARRAY:
        return $this->result->fetch_row();
      case RESULT_ASSOC:
      default:
        return $this->result->fetch_assoc();
      case RESULT_OBJECT:
        return $this->result->fetch_object();
    }
  }

  /**
   * @inheritdoc
   */
  public function fetchAll($mode=RESULT_ASSOC){
    if(!method_exists($this->result, 'fetch_all')){
      $result=array();
      while($row=$this->fetch($mode)){
        $result[]=$row;
      }
      return $result;
    }
    else{
      switch($mode){
        case RESULT_ARRAY:
          return $this->result->fetch_all(MYSQLI_NUM);
        case RESULT_ASSOC:
        default:
          return $this->result->fetch_all(MYSQLI_ASSOC);
        case RESULT_OBJECT:
          throw new DBException("Fetch All with objects is unsupported!");
      }
    }
  }

  /**
   * @inheritdoc
   */
  public function numRows(){
    return $this->result->num_rows;
  }

  /**
   * @inheritdoc
   */
  public function free(){
    $this->result->free();
  }

  public function __destruct(){
    $this->free();
  }

  /**
   * Resets the internal results pointer, so the next call to fetch() will return the first record in the dataset
   */
  public function reset(){
    $this->result->data_seek(0);
  }
}
