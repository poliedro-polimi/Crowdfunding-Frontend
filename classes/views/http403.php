<?php
use nigiri\exceptions\FileNotFound;
use nigiri\views\Html;

/** @var FileNotFound $exception */
?>
<h1>Accesso Vietato</h1>
<p>Non hai i permessi necessari per visualizzare questa pagina.<br />
Se non hai fatto login prova a eseguirlo e dopo tenta di nuovo ad accedere a questa pagina. Se l&#039;hai gi&agrave; eseguito allora non hai i permessi corretti per accedere.</p>
<p>Se ritieni che questo sia un errore contatta gli amministratori del sito.</p>
<p>Dettaglio dell&#039;errore: <?= Html::escape($exception->getMessage()) ?></p>
