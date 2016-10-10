<?php
namespace site;
use site\Exception;

/**
 * Email Sender, wrapper around PHPMailer
 */
class Email
{
    /**
     * @var \PHPMailer
     */
    private $mail;

    public function __construct()
    {
        require_once(dirname(__DIR__) . '/libraries/PHPMailer/class.phpmailer.php');
        $this->mail = new \PHPMailer();
        $this->mail->PluginDir = dirname(__DIR__).'/libraries/PHPMailer/';
        $this->mail->CharSet = 'utf-8';
    }

    /**
     * Invia un'email in UTF-8 (con Allegato)
     * @param string $subject : oggetto dell'email
     * @param string $message : il messaggio da inviare, può anche essere il percorso di un file
     * @param bool $html : indica se $message è in html
     * @param array $data : i dati da sostituire nel testo dell'email, le chiavi dell'array saranno richiamabili nel testo come @chiave. I dati nella chiave php_data vengono invece passati come variabili php se $message è un file
     * @param array $from : opzionale. un array con le informazioni del mittente nelle chiavi 'name' e 'addr'
     * @param array $header : opzionale. header addizionali da inviare. Il nome passato come chiave nell'array, il valore nel valore
     * @return True se l'invio è andato a buon fine. False altrimenti
     */
    public function send(
      $subject,
      $message,
      $html = false,
      $data = array(),
      $from = array(),
      $header = null
    ) {
        require_once(dirname(__DIR__) . '/libraries/PHPMailer/class.phpmailer.php');

        try {
            //Invio con SMTP
            if (getParam('email_smtp_active', 0) == 1) {
                $this->mail->IsSMTP();
                $this->mail->Host = getParam('email_smtp', '');
                $this->mail->Port = getParam('email_smtp_port', '25');
                $user = getParam('email_smtp_user', '');
                if (!empty($user)) {
                    $this->mail->SMTPAuth = true;
                    $this->mail->Username = $user;
                    $this->mail->Password = getParam('email_smtp_psw', '');
                }
                if (getParam('email_smtp_secure') == 1) {
                    $this->mail->SMTPSecure = "ssl";
                }
            }

            if (empty($from) || empty($from['addr'])) {
                $this->mail->SetFrom($par_WebmasterEmail, $par_SiteName, true);
            } else {
                if (!isset($from['name'])) {
                    $from['name'] = '';
                }
                $this->mail->SetFrom($from['addr'], $from['name'], true);
                $this->mail->Sender = $par_WebmasterEmail;
            }

            $this->mail->Subject = $subject;

            if (file_exists(dirname(__DIR__) . '/email/' . $message)) {
                $message = page_include(dirname(__DIR__) . '/email/' . $message,
                  array_merge(['email' => $this->mail], !empty($data['php_data']) ? $data['php_data'] : array()));
            } elseif (file_exists($message)) {
                $message = page_include($message,
                  array_merge(['email' => $this->mail], !empty($data['php_data']) ? $data['php_data'] : array()));
            }

            if ($html && empty($data['no_layout'])) {
                $message = page_include(dirname(__DIR__) . '/email/layout.php', [
                  'message' => $message,
                  'subject' => $subject,
                  'email' => $this->mail
                ]);
            }

            if (isset($data['php_data'])) {
                unset($data['php_data']);
            }

            $this->mail->Body = $this->tokens_substitution($message, $data, $html);

            if (!empty($header)) {
                foreach ($header as $key => $value) {
                    $this->mail->AddCustomHeader($key, $value);
                }
            }

            $ret = $this->mail->Send();
            if (!$ret) {
                Exception::watchdog("Errore invio email (send method): " . $this->mail->ErrorInfo);

                return false;
            }
        } catch (\phpmailerException $e) {
            Exception::watchdog("Errore invio email (eccezione): " . $e->errorMessage()); //Pretty error messages from PHPMailer
            return false;
        }

        return true;
    }

    public function addRecipients($to){
        if (is_array($to)) {
            if ($this->is_email_array($to)) {
                $to['to'][] = $to;
            }
            $enter = false;
            if (isset($to['to'])) {
                $this->mail_recipient_adding($to, 'to', 'AddAddress');
                $enter = true;
            }
            if (isset($to['cc'])) {
                $this->mail_recipient_adding($to, 'cc', 'AddCC');
                $enter = true;
            }
            if (isset($to['bcc'])) {
                $this->mail_recipient_adding($to, 'bcc', 'AddBCC');
                $enter = true;
            }
            if (!$enter) {//Last Resort. It is just an array of strings, each one email address
                $this->mail_recipient_adding(array('to' => $to), 'to', 'AddAddress');
            }
        } else {
            $this->mail->AddAddress($to);
        }

        return $this;
    }

    public function addAttachment($path, $name=''){
        $this->mail->AddAttachment($path, $name);
    }

    private function is_email_array($arr)
    {
        if (isset($arr['addr'])) {
            return true;
        }

        return false;
    }

    private function mail_recipient_adding($arr, $key, $met)
    {
        if ($this->is_email_array($arr[$key])) {
            $arr[$key][] = $arr[$key];
            unset($arr[$key]['addr']);
            unset($arr[$key]['name']);
        }
        foreach ($arr[$key] as $recipient) {
            if (is_string($recipient)) {
                $this->mail->$met($recipient);
            } else {
                if (!empty($recipient['addr'])) {
                    if (!isset($recipient['name'])) {
                        $recipient['name'] = '';
                    }
                    $this->mail->$met($recipient['addr'], $recipient['name']);
                }
            }
        }
    }

    private function tokens_substitution($str, $tokens, $escape_html = false)
    {
        $patterns = [
          '/@site_name\b/s',
          '/@site_url\b/s',
          '/@year\b/s'
        ];
        $values = [
          $GLOBALS['par_SiteName'],
          $GLOBALS['par_SiteURL'],
          date('Y')
        ];
        foreach ($tokens as $k => $v) {
            $patterns[] = '/@' . preg_quote($k, '/') . '\b/s';
            if ($escape_html) {
                $values[] = escape($v, 'html');
            } else {
                $values[] = $v;
            }
        }

        return preg_replace($patterns, $values, $str);
    }
}
