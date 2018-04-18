<?php

/** @var bool $reward */
/** @var string $donation_id */
/** @var bool $mail_fail */

use nigiri\Site;
use nigiri\views\Html;
use nigiri\views\Url;

Site::getTheme()->append(Url::resource("assets/imgs/banner3.png"), 'banner_img');
?>
<section id="confirmation">
    <div class="container-fluid">
<h1 class="section-title"><?= Html::escape(l("Donazione confermata!")); ?></h1>
<h3 class="section-title"><?= Html::escape(l('Ti ringraziamo per la tua donazione a sostegno delle attività di PoliEdro!')); ?></h3>
<p class="section-content"><?= Html::escape(l('Queste donazioni ci permettono di far fronte alle spese di tutti i giorni per la gestione dell\'associazione')); ?> <a href="http://polimipride.it"><?= Html::escape(l('e per sostenere la realizzazione di iniziative come PoliMi Pride'));?></a>
</p>
    <div id="optional_info" class="section-content">
<?php if(!empty($donation_id)): ?>
<p class="text-muted" id="donation"><?= Html::escape(l("L'identificativo della tua donazione è %s, se hai bisogno di contattarci 
ricordati di includerlo nel tuo messaggio!", $donation_id)); ?></p>
<?php
    endif;

    if(!$mail_fail):?>
        <p class="text-success" id="email"><?= Html::escape(l("Ti abbiamo inviato per email tutte le informazioni di conferma della tua donazione"))
            ?></p>
    <?php else: ?>
        <p class="text-warning" id="email"><?= Html::escape(l("Non siamo riusciti a inviarti l'email di conferma della tua donazione!")
            ) ?>
            <?php if($reward){
            echo '<br />'.Html::escape(l("Segnati l'identificativo della tua donazione, ti servirà per 
        ritirare i tuoi gadget! Se desideri essere sicuro contattaci, ti invieremo manualmente l'email di conferma!"));
        } ?>
        </p>
<?php
    endif;

    if($reward): ?>
<p id="rewards"><?= Html::escape(l('Durante la donazione hai selezionato delle ricompense da ricevere: ti contatteremo per email non appena tutti i materiali saranno pronti!')); ?><br />
    <?= Html::escape(l('Ti ricordiamo che i gadget PoliMi Pride saranno consegnati agli stand nei campus del Politecnico di Milano
    durante il mese di Giugno. Per restare informato sulle date esatte seguici su nostri social e tieni d\'occhio la
    tua casella email.')) ?>
</p>
<?php endif;
?>
    </div>
    </div>
</section>
