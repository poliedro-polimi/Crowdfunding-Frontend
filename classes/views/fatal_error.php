<?php
use nigiri\db\DBException;
use nigiri\exceptions\Exception;
use nigiri\exceptions\PHPErrorException;
use nigiri\Site;
use nigiri\views\Html;

/** @var Exception $exception */
?>

<h1>Errore</h1>
<h2>Si &egrave; verificato un errore grave che impedisce la corretta esecuzione del Sito Web.</h2>
<h3>Ci scusiamo per il disagio, il nostro staff &egrave; stato notificato e il problema sar&agrave; presto risolto.</h3>
<p>Se avete necessit&agrave; di contattare lo staff potete farlo inviando un email a: <a
      href="mailto:<?= Site::getParam('email') ?>"><?= Html::escape(Site::getParam('email'))?></a></p>
<p>Dettaglio dell&#039;errore: <?= (($exception!=null and $exception instanceof Exception and $exception->getMessage())?
    Html::escape($exception->getMessage()):'Nessuno')?></p>
<?php
if(Site::getParam('debug') and $exception!=null){
    if($exception instanceof Exception) {
        echo '<table><tr><th colspan="2">Exception</th></tr>
    <tr><td>Nome</td><td>' . get_class($exception) . '</td></tr>
    <tr><td>Messaggio</td><td>' . Html::escape($exception->getMessage()) . '</td></tr>';
        if($exception instanceof Exception) {
            echo '<tr><td>Messaggio Interno</td><td>' . nl2br(Html::escape($exception->getInternalError())) . '</td></tr>';
        }
        if ($exception instanceof PHPErrorException) {
            echo '<tr><td>Contesto</td><td>' . Html::escape($exception->getContextAsString()) . '</td></tr>';
        } elseif ($exception instanceof DBException) {
            echo '<tr><td>Query</td><td>' . Html::escape($exception->getQuery()) . '</td></tr>';
        }
        echo '<tr><td>Codice</td><td>' . $exception->getCode() . '</td></tr>
    <tr><td>File</td><td>' . Html::escape($exception->getFile()) . '</td></tr>
    <tr><td>Line</td><td>' . Html::escape($exception->getLine()) . '</td></tr>
    <tr><td>Call Trace</td><td><pre>';
        $t = $exception->getTrace();
        var_dump($t);
        echo '</pre></td></tr>';
        echo '</table>';
    }
    else{
        var_dump($exception);
    }
}
