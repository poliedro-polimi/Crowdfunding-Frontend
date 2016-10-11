<?php
use nigiri\db\DBException;
use nigiri\exceptions\Exception;
use nigiri\exceptions\PHPErrorException;
use nigiri\views\Html;

/**
 * Takes out the Byte Order Masks in an UTF8 text. Useful for includes that use UTF8 encoded files because BOMs break HTML validity and doctype
 * @param string $buffer
 * @return string $buffer without the BOM
 */
function no_bom($buffer) {
    return str_replace("\xef\xbb\xbf", '', $buffer);
}

/**
 * Used to include a page in the current page. It includes variable scope isolation and possibility to send specific
 * variables to the included file. File is not printed directly in the output but it is returned as an output string
 * @param string $path the path of the file to include
 * @param array $vars an array of variables to pass to the included file @see extract()
 * @return string the final output of the included file
 */
function page_include($path, $vars = array()) {
    if (file_exists($path)) {
        ob_start();
        extract($vars);
        include($path);
        $output = no_bom(ob_get_contents());
        ob_end_clean();
        return $output;
    }
}

/**
 * Gestore degli errori, intecetta gli errori di PHP e li tramuta in PHPErrorException
 * @param null $errno
 * @param null $errstr
 * @param string $errfile
 * @param string $errline
 * @param array $errcontext
 * @throws PHPErrorException
 */
function error_to_exception_handler($errno, $errstr, $errfile='', $errline='', $errcontext=array()){
    if(error_reporting() & $errno){
        $e= new PHPErrorException($errno, $errstr, $errfile, $errline, $errcontext);
        if(PHPErrorException::isFatal($errno)){
            throw $e;
        }
        else{
            if(ini_get('log_errors')){
                $e->logToErrorLog();
            }
            else {
                if (defined('DEBUG') and DEBUG) {
                    echo '<p class="error">' . nl2br(Html::escape($e->renderFullError())) . "</p>";
                }
                else {
                    echo '<p class="error">' . $e->showError() . '</p>';
                }
            }
        }
    }
}

/**
 * Redirects to error_to_exception_handler() all of fatal errors that don't get intercepted with set_error_handler()
 * @throws PHPErrorException
 */
function fatal_error_handler(){
    $last = error_get_last();
    if($last===null){
        return;
    }
    /*
     * Throw only if it's a fatal error, otherwise we risk throwing
     * two times for the same error as it would have been already thrown with set_error_handler()
     */
    elseif(PHPErrorException::isFatal($last['type'])){
        //Uncaught Exceptions handler has already been unregistered at this point! We need to manually catch and redirect
        try {
            error_to_exception_handler($last['type'], $last['message'], $last['file'], $last['line']);
        }
        catch(Exception $e){
            uncaught_exception_handler($e);
        }
    }
}

/**
 * Gestisce le uncaught exception
 * @param $e \Exception
 */
function uncaught_exception_handler($e){
    if($e instanceof Exception){
        if(ini_get('log_errors')) {
            $e->logToErrorLog();
        }

        try{//if possibile log to db
            $e->logError('Catch di emergenza',true);
        }
        catch(DBException $ex) {//if db is unavailable, log to email
            $e->logToWebmasterEmail();
        }
        render_fatal_error($e);
    }
    else{
        render_fatal_error($e);
    }
}

/**
 * Renders the Fatal Error/Program Panic screen
 * @param $exception Exception
 */
function render_fatal_error($exception=null) {
    while(ob_get_level()>0){
        ob_end_clean();
    }

    echo page_include(__DIR__.'includes/full_error.php', array('exception' => $exception));
    exit();
}
