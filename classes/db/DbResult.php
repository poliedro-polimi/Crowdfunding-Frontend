<?php
namespace nigiri\db;
/**
 * The interface for all the results returned by the DB layer
 */
interface DbResult{
    /**
     * Fetches the next row from the resultset of the query
     * @param $mode: Specifies the format of the returned result.
     * @return array|\StdClass the row of data in the format specified by $mode: RESULT_ARRAY returns an array (int
     * indexes), RESULT_ASSOC returns an associative array, RESULT_OBJECT returns an object of StdClass
     */
    public function fetch($mode=DB::RESULT_ASSOC);

    /**
     * Fetches all the rows from the resultset of the query
     * @param $mode: Specifies the format of the singles rows of data in the returned result.
     * @return array|\StdClass[] an array containing all the rows of data in the format specified by $mode:
     * RESULT_ARRAY returns an array (int indexes),
     * RESULT_ASSOC returns an associative array,
     * RESULT_OBJECT returns an object of StdClass
     */
    public function fetchAll($mode=DB::RESULT_ASSOC);

    /**
     * @return int the number of rows in the resultset
     */
    public function numRows();

    /**
     * Frees the memory occupied by the resultset
     */
    public function free();

    /**
     * Resets the internal results pointer, so the next call to fetch() will return the first record in the dataset
     */
    public function reset();
}
