<?php
use nigiri\views\Url;
use nigiri\views\Html;
use site\controllers\DonationController;
?>
<section id="how_to_help">
    <div class="container">
        <h1 class="section-title"><?= Html::escape(l('Come sostenere il progetto?')); ?></h1>
        <div class="section-content"><?= Html::escape(l('Quest\'anno PoliMi Pride ha tanti gadget colorati in più! Le sacche/zainetto, gli adesivi e le spille completano il set di gadget targati "Polimi Pride" insieme alla maglietta!')) ?><br /><?= Html::escape(l('Colleziona tutti i gadget con il logo rinnovato in edizione 2018 con una donazione volontaria a sostegno delle attività di PoliEdro.')) ?></div>
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
                <a href="<?= Url::to('donation', ['amount'=> DonationController::REWARD_THRESHOLD1]) ?>"><?=
                    Html::escape(l('Dona')) ?></a>
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
                <a href="<?= Url::to('donation', ['amount'=> DonationController::REWARD_THRESHOLD2]) ?>"><?=
                    Html::escape(l('Dona')); ?></a>
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
                <a href="<?= Url::to('donation', ['amount' => DonationController::REWARD_THRESHOLD3]) ?>"><?=
                    Html::escape(l('Dona')); ?></a>
            </div>
            <div class="reward-description">
                <?= Html::escape(l("Non perderti la maglietta PoliMi Pride! Con il suo design rinnovato è disponibile sia in formato t-shirt che come canotta, scegli quella che fa più per te!")); ?><br /><?= Html::escape(l('(Sacca, spilla e adesivi inclusi)')) ?>
            </div>
        </div>
    </div>
</section>
<section id="donate">
    <a href="<?= Url::to('donation') ?>" class="btn"><?= l('Ricevi'); ?></a>
</section>
