<?php
namespace site;

class Exception extends \Exception
{
    private $internal;

    public function __construct($str = null, $no = null, $detail = null)
    {
        parent::__construct($str, $no);
        $this->internal = $detail;
    }

    public function showError($return = false)
    {
        $str = '<div class="error">' . escape($this->getMessage(), 'html') . "</div>";
        if (!$return) {
            echo $str;
        } else {
            return $str;
        }
    }

    public function showAndLogError($return = false)
    {
        $this->logError();
        $str = '<div class="error">' . escape($this->getMessage(), 'html') . "</div>";
        if (!$return) {
            echo $str;
        } else {
            return $str;
        }
    }

    public function logError($additional = '', $save_trace = false)
    {
        if (!empty($this->internal)) {
            $trace = '';
            if ($save_trace) {
                $trace = "\n Call Stack:\n";
                ob_start();
                $t = $this->getTrace();
                var_dump($t);
                $trace .= ob_get_contents();
                ob_end_clean();
            }

            $this->watchdog($additional . ' - ' . $this->renderFullError() . $trace);
        }
    }

    public function renderFullError()
    {
        return $this->getMessage() . " [" . $this->getInternalError() . "]";
    }

    public function getInternalError()
    {
        return $this->internal;
    }

    /**
     * Writes the full error description in the PHP error log, if it's enabled
     */
    public function logToErrorLog()
    {
        error_log($this->renderFullError());
    }

    public function logToWebmasterEmail()
    {
        $email = $GLOBALS['par_TechnicalEmail'];
        if (empty($email)) {
            if (!empty($GLOBALS['par_WebmasterEmail'])) {
                $email = $GLOBALS['par_WebmasterEmail'];
            } else {
                $email = 'webmaster';
            }
        }

        @email($email, "BSS: Errore Fatale!", $this->renderEmailText());
    }

    protected function renderEmailText()
    {
        ob_start();
        $t = $this->getTrace();
        var_dump($t);
        $stack = ob_get_contents();
        ob_end_clean();

        return "Si è verificato un errore fatale su BSS!

    Errore generico: " . $this->getMessage() . "
    Dettaglio Errore: " . $this->renderFullError() . "

    Pagina Richiesta: " . $_GET['front_controller_page'] . "

    Call Stack:" . $stack;
    }

    /**
     * Aggiunge una linea nel log degli errori
     * @param $msg : il messaggio da inserire
     * @oaram $user: opzionale, l'utente che ha eseguito l'azione che ha scatenato l'errore
     */
    static public function watchdog($msg, $user = "")
    {
        if (!$user && isset($_SESSION['UID'])) {
            $user = getUsername($_SESSION['UID']);
        }
        try {
            DB::query("INSERT INTO LogErrori (Nome, Errore, DataEvento, IP) VALUES ('" . escape($user) . "','" . escape($msg) . "',NOW(),'" . escape($_SERVER['REMOTE_ADDR']) . "')");
        } catch (DBException $e) {
            //Se fallisce perfino questo...registriamo l'errore con l'handler di default di PHP
            error_log($msg);
        }
    }
}
