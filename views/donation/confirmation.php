<?php

/** @var bool $reward */
/** @var string $donation_id */

use nigiri\Site;
use nigiri\views\Html;
use nigiri\views\Url;

Site::getTheme()->append(Url::resource("assets/imgs/banner3.png"), 'banner_img');
?>
<h1><?= Html::escape(l("Donazione confermata!")); ?></h1>
<h3><?= Html::escape(l('Ti ringraziamo per la tua donazione a sostegno delle attività di PoliEdro!')); ?></h3>
<p><?= Html::escape(l('Queste donazioni ci permettono di far fronte alle spese di tutti i giorni per la gestione dell\'associazione e per 
sostenere la realizzazione di iniziative come'))?> <a href="http://polimipride.it">PoliMi Pride</a>
</p>
<p class="text-muted"><?= Html::escape(l("L'identificativo della tua donazione è %s, se hai bisogno di contattarci 
ricordati di includerlo nel tuo messaggio!")); ?></p>
<?php if($reward): ?>
<p><?= Html::escape(l('Durante la donazione hai selezionato delle ricompense da ricevere: ti contatteremo per email non appena tutti i materiali saranno pronti!')); ?><br />
    <?= Html::escape(l('Ti ricordiamo che i gadget PoliMi Pride saranno consegnati agli stand nei campus del Politecnico di Milano
    durante il mese di Giugno. Per restare informato sulle date esatte seguici su nostri social e tieni d\'occhio la
    tua casella email.')) ?>
</p>
<?php endif;
