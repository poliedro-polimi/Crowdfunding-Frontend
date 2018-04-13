<?php
use nigiri\views\Url;
use nigiri\views\Html;
use site\controllers\DonationController;
?>
<section id="how_to_help">
    <div class="container">
        <h1 class="section-title"><?= Html::escape(l('Come sostenere il progetto?')); ?></h1>
        <div class="section-content"><?= Html::escape(l('Quest\'anno PoliMi Pride ha tanti gadget colorati in più! Le sacche/zainetto, gli adesivi e le spille completano il set di gadget targati "Polimi Pride" insieme alla maglietta!')) ?><br /><?= l('Ma per realizzarli abbiamo bisogno del vostro sostegno: abbiamo lanciato una <strong>campagna di crowdfunding</strong> per raccogliere le partecipazioni e mettere insieme i fondi per ordinare i materiali targati PolimiPride.') ?>
        </div>
    </div>
</section>
<section id="how_it_works">
    <div class="container">
        <h1 class="section-title"><?= Html::escape(l('Come funziona?')); ?></h1>
        <div class="section-content"><?= Html::escape(l('Il funzionamento dell\'iniziativa è molto semplice e si riassume in pochi semplici passi:'))
            ?>
            <ol>
                <li><?= Html::escape(l('Fai una donazione a PoliEdro dell\ammontare che preferisci')) ?></li>
                <li><?= Html::escape(l('In base all\'ammontare della tua donazione scegli il pacchetto di gadget che più ti piace'))
                    ?></li>
                <li><?= Html::escape(l('Fino all\'11 Giugno 2018 il crowdfunding resterà aperto alle donazioni.'))?></li>
                <li><?= Html::escape(l('Alla chiusura del crowdfunding partiranno tutti gli ordini dei gadget, che avremo pronti da distribuire durante la Pride Week ai nostri stand nei campus (Leonardo e Bovisa) del Politecnico')) ?></li>
                <li><?= Html::escape(l('Per ritirare i tuoi gadget presentati agli stand con il tuo numero di donazione o l\'email di conferma')) ?></li>
            </ol></div>
    </div>
</section>
<section id="sequence">
    <?= page_include(dirname(dirname(__DIR__)).'/assets/imgs/infographics.svg') ?>
</section>
<section id="rewards">
    <h1 class="section-title"><?= Html::escape(l('Abbiamo Realizzato Per Te...')); ?></h1>
    <div class="row justify-content-around no-gutters">
        <div class="col-10 col-md-5 col-lg-3 col-xl-2 reward">
            <div class="reward-box">
                <img src="<?= Url::resource("assets/imgs/sacca.png"); ?>" alt="<?= Html::escape(l('Sacca Zainetto PoliMi Pride')); ?>" />
                <div class="reward-contribution">
                    <div class="contribution-label"><?= Html::escape(l('Contributo Minimo Volontario')); ?>:</div>
                    <div class="reward-amount"><?= DonationController::REWARD_THRESHOLD1 ?>&euro;</div>
                </div>
                <a href="<?= Url::to('donation', ['reward'=> 1]) ?>"><?= Html::escape(l('Dona')) ?></a>
            </div>
            <div class="reward-description">
                <?= Html::escape(l('Le sacche zainetto PoliMi Pride sono perfette per affrontare la parata del Milano Pride! Le sacche in nylon 34x43cm sono utilissime per portarsi dietro bottiglie d\'acqua, bandiere e tutto il necessario per essere favolosi!')) ?>
            </div>
        </div>
        <div class="col-10 col-md-5 col-lg-3 col-xl-2 reward">
            <div class="reward-box">
                <img src="<?= Url::resource('assets/imgs/adesivi_spille.png') ?>" alt="Spille, Adesivi e Shopper PoliMi Pride" />
                <div class="reward-contribution">
                    <div class="contribution-label"><?= Html::escape(l('Contributo Minimo Volontario')); ?>:</div>
                    <div class="reward-amount"><?= DonationController::REWARD_THRESHOLD2 ?>&euro;</div>
                </div>
                <a href="<?= Url::to('donation', ['reward'=> 2]) ?>"><?= Html::escape(l('Dona')); ?></a>
            </div>
            <div class="reward-description">
                <?= Html::escape(l("Mostra il tuo orgoglio! Con gli adesivi e la spilla PoliMi Pride potrai mostrare ovunque il tuo supporto all'iniziativa. Usali per personalizzare i tuoi oggetti, dal pc al tuo zaino!")); ?><br /><?= Html::escape(l('(Sacca zainetto inclusa)')) ?>
            </div>
        </div>
        <div class="col-10 col-md-6 col-lg-3 col-xl-2 reward">
            <div class="reward-box">
                <img src="<?= Url::resource('assets/imgs/maglia.png') ?>" />
                <div class="reward-contribution">
                    <div class="contribution-label"><?= Html::escape(l('Contributo Minimo Volontario')); ?>:</div>
                    <div class="reward-amount"><?= DonationController::REWARD_THRESHOLD3 ?>&euro;</div>
                </div>
                <a href="<?= Url::to('donation', ['reward' => 3]) ?>"><?= Html::escape(l('Dona')); ?></a>
            </div>
            <div class="reward-description">
                <?= Html::escape(l("Non perderti la maglietta PoliMi Pride! Con il suo design rinnovato è disponibile sia in formato t-shirt che come canotta, scegli quella che fa più per te!")); ?><br /><?= Html::escape(l('(Sacca, spilla e adesivi inclusi)')) ?>
            </div>
        </div>
    </div>
</section>
<section id="donate">
    <a href="<?= Url::to('donation') ?>" class="btn"><?= Html::escape(l('Ricevi')); ?></a>
</section>
