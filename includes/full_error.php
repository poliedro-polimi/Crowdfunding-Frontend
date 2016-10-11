<?php
/* @var $exception \nigiri\Exception */

use site\db\DBException;
use site\Exception;
use site\PHPErrorException;
use site\Site;
use site\views\Html;

?>
<!doctype html>
<html>
<head>
<style type="text/css">

</style>
<title>Errore Fatale</title>
</head>
<body>
<h1>Errore Fatale</h1>
<h2>Si &egrave; verificato un errore grave che impedisce la corretta esecuzione del Sito Web.</h2>
<h3>Ci scusiamo per il disagio, il nostro staff &egrave; stato notificato e il problema sar&agrave; presto risolto.</h3>
<p>Se avete necessit&agrave; di contattare lo staff potete farlo inviando un email a: <a
    href="mailto:<?= Site::getParam('email') ?>"><?= Html::escape(Site::getParam('email')); ?></a></p>
<p>Dettaglio dell&#039;errore: <?php
  echo (($exception!=null and $exception instanceof Exception and $exception->getMessage())?
    Html::escape($exception->getMessage()):'Nessuno'); ?></p>
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
      echo '<tr><td>Contesto</td><td>' . $exception->getContextAsString() . '</td></tr>';
    } elseif ($exception instanceof DBException) {
      echo '<tr><td>Query</td><td>' . $exception->getQuery() . '</td></tr>';
    }
    echo '<tr><td>Codice</td><td>' . $exception->getCode() . '</td></tr>
    <tr><td>File</td><td>' . $exception->getFile() . '</td></tr>
    <tr><td>Line</td><td>' . $exception->getLine() . '</td></tr>
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
?>
</body>
</html>
