<?php
use site\db\DBException;
use site\Exception;
use site\PHPErrorException;
use site\Psr4AutoloaderClass;
use site\Site;

require_once __DIR__.'/classes/class_loader.php';

ini_set('display_errors', false);
set_error_handler('error_to_exception_handler', E_ALL);
set_exception_handler('uncaught_exception_handler');
register_shutdown_function('fatal_error_handler');

$autoloader = new Psr4AutoloaderClass();
$autoloader->addNamespace('site', __DIR__.'/classes');
$autoloader->register();

$site = new Site(require_once __DIR__.'/includes/settings.php');

/**
 * Gestore degli errori, intecetta gli errori di PHP e li tramuta in PHPErrorException
 * @param null $errno
 * @param null $errstr
 * @param null $errfile
 * @param null $errline
 * @param null $errcontext
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
                    echo '<p class="error">' . nl2br(escape($e->renderFullError(), 'html')) . "</p>";
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
    if($e instanceof \Exception){
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
