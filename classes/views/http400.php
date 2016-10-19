<?php
use nigiri\exceptions\FileNotFound;
use nigiri\views\Html;

/** @var FileNotFound $exception */
?>
<h1>Richiesta Errata</h1>
<p>I dati che hai inviato sembrano essere sbagliati! Se hai scritto a mano l&#039;URL della pagina controlla che l&#039;indirizzo sia corretto insieme a tutti i parametri!</p>
<p>Dettaglio dell&#039;errore: <?= Html::escape($exception->getMessage()) ?></p>
