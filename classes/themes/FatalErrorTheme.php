<?php
namespace nigiri\themes;

use nigiri\db\DBException;
use nigiri\exceptions\Exception;
use nigiri\exceptions\PHPErrorException;
use nigiri\Site;
use nigiri\views\Html;

class FatalErrorTheme implements ThemeInterface{
    private $exception;

    public function append($str, $part = 'body')
    {
        if($part=='exception'){
            $this->exception = $str;
        }
    }

    public function render()
    {
        echo '
<!doctype html>
<html>
<head>
<style type="text/css">

</style>
<title>Errore Fatale</title>
</head>
<body>
<h1>Errore</h1>
<h2>Si &egrave; verificato un errore grave che impedisce la corretta esecuzione del Sito Web.</h2>
<h3>Ci scusiamo per il disagio, il nostro staff &egrave; stato notificato e il problema sar&agrave; presto risolto.</h3>
<p>Se avete necessit&agrave; di contattare lo staff potete farlo inviando un email a: <a
    href="mailto:'. Site::getParam('email') .'">'. Html::escape(Site::getParam('email')).'</a></p>
<p>Dettaglio dell&#039;errore: '. (($this->exception!=null and $this->exception instanceof Exception and $this->exception->getMessage())?
    Html::escape($this->exception->getMessage()):'Nessuno').'</p>
';
        if(Site::getParam('debug') and $this->exception!=null){
            if($this->exception instanceof Exception) {
                echo '<table><tr><th colspan="2">Exception</th></tr>
    <tr><td>Nome</td><td>' . get_class($this->exception) . '</td></tr>
    <tr><td>Messaggio</td><td>' . Html::escape($this->exception->getMessage()) . '</td></tr>';
                if($this->exception instanceof Exception) {
                    echo '<tr><td>Messaggio Interno</td><td>' . nl2br(Html::escape($this->exception->getInternalError())) . '</td></tr>';
                }
                if ($this->exception instanceof PHPErrorException) {
                    echo '<tr><td>Contesto</td><td>' . $this->exception->getContextAsString() . '</td></tr>';
                } elseif ($this->exception instanceof DBException) {
                    echo '<tr><td>Query</td><td>' . $this->exception->getQuery() . '</td></tr>';
                }
                echo '<tr><td>Codice</td><td>' . $this->exception->getCode() . '</td></tr>
    <tr><td>File</td><td>' . $this->exception->getFile() . '</td></tr>
    <tr><td>Line</td><td>' . $this->exception->getLine() . '</td></tr>
    <tr><td>Call Trace</td><td><pre>';
                $t = $this->exception->getTrace();
                var_dump($t);
                echo '</pre></td></tr>';
                echo '</table>';
            }
            else{
                var_dump($this->exception);
            }
        }
        echo '
    </body>
    </html>';
    }
}
