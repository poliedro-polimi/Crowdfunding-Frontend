<?php
namespace nigiri\db;

class MySqlResult implements DbResult{
    private $result;

    public function __construct(\mysqli_result $result){
        if($result instanceof \mysqli_result){
            $this->result=$result;
        }
        else{
            throw new DBException("Invalid result resource type!");
        }
    }

    /**
     * @inheritdoc
     */
    public function fetch($mode=DB::RESULT_ASSOC){
        switch($mode){
            case DB::RESULT_ARRAY:
                return $this->result->fetch_row();
            case DB::RESULT_ASSOC:
            default:
                return $this->result->fetch_assoc();
            case DB::RESULT_OBJECT:
                return $this->result->fetch_object();
        }
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($mode=DB::RESULT_ASSOC){
        if(!method_exists($this->result, 'fetch_all')){
            $result=array();
            while($row=$this->fetch($mode)){
                $result[]=$row;
            }
            return $result;
        }
        else{
            switch($mode){
                case DB::RESULT_ARRAY:
                    return $this->result->fetch_all(MYSQLI_NUM);
                case DB::RESULT_ASSOC:
                default:
                    return $this->result->fetch_all(MYSQLI_ASSOC);
                case DB::RESULT_OBJECT:
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

