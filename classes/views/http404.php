<?php
use nigiri\exceptions\FileNotFound;
use nigiri\views\Html;

/** @var FileNotFound $exception */
?>
<h1>Pagina Non Trovata</h1>
<p>La pagina che hai richiesto non &egrave; stata trovata! Controlla che l&#039;indirizzo che hai specificato sia corretto!</p>
<p>Dettaglio dell&#039;errore: <?= Html::escape($exception->getMessage()) ?></p>
