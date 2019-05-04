<?php

use nigiri\Site;
use nigiri\views\Url;
use nigiri\views\Html;
use site\controllers\DonationController;

?>
<section id="how_to_help">
    <div class="container container-narrow">
        <h1 class="section-title"><?= Html::escape(l('Come sostenere il progetto?')); ?></h1>
        <div class="section-content"><?= Html::escape(l('I gadget colorati di PoliMi Pride sono sempre di più! Le sacche/zainetto, le spille, il bracciale e le tira lampo completano il set di gadget targati "Polimi Pride" insieme alla maglietta!')) ?>
            <br/><?= l('Ma per realizzarli abbiamo bisogno del vostro sostegno: come lo scorso anno tutti i gadget sono finanziati direttamente da PoliEdro con il vostro aiuto! Contribuendo alla campagna di autofinanziamento PoliMi Pride 2019 potremo realizzare i materiali per tutti quanti e riceverai i tuoi durante la Pride Week al Politecnico di Milano!') ?>
        </div>
    </div>
</section>
<section id="how_it_works">
    <div class="container container-narrow">
        <h1 class="section-title"><?= Html::escape(l('Come funziona?')); ?></h1>
        <div class="section-content"><?= Html::escape(l('Il funzionamento dell\'iniziativa è molto semplice e si riassume in pochi passi.'))
            ?>
            <ol>
                <li><?= Html::escape(l('Fai una donazione a PoliEdro dell\'ammontare che preferisci')) ?></li>
                <li><?= Html::escape(l('In base all\'ammontare della tua donazione potrai scegliere un pacchetto di gadget da ricevere'))?></li>
                <li><?= Html::escape(l('Fino al <strong>9 Giugno</strong> il crowdfunding resterà aperto alle donazioni')) ?></li>
                <li><?= Html::escape(l('Alla chiusura del crowdfunding partiranno tutti gli ordini dei gadget, che avremo pronti da distribuire durante la Pride Week (24-28 Giugno) ai nostri stand nei campus del Politecnico (Leonardo e Bovisa)')) ?></li>
                <li><?= Html::escape(l('Per ritirare i tuoi gadget presentati agli stand con il tuo numero di donazione o l\'email di conferma')) ?></li>
            </ol>
        </div>
    </div>
</section>
<section id="sequence">
    <?= page_include(dirname(dirname(__DIR__)) . '/assets/imgs/infographics_' . Site::getRouter()->getRequestedLanguage() . '.svg') ?>
</section>
<section id="rewards">
    <div class="container container-narrow">
        <h1 class="section-title"><?= Html::escape(l('Abbiamo Realizzato Per Te...')); ?></h1>
    </div>
    <div class="row justify-content-around no-gutters">
        <div class="col-10 col-md-5 col-lg-3 reward">
            <div class="reward-box">
                <img src="<?= Url::resource("assets/imgs/tier1.png"); ?>"
                     alt="<?= Html::escape(l('Sacca Zainetto, Bracciale, Spille e Tira Lampo PoliMi Pride')); ?>"/>
                <div class="reward-contribution">
                    <div class="contribution-label"><?= Html::escape(l('Contributo Minimo')); ?>:</div>
                    <div class="reward-amount"><?= DonationController::REWARD_THRESHOLD1 ?>&euro;</div>
                </div>
                <a href="<?= Url::to('donation', ['reward' => 1]) ?>"><?= Html::escape(l('Dona')) ?></a>
            </div>
            <div class="reward-description">
                <?= Html::escape(l('Le sacche zainetto PoliMi Pride sono perfette per affrontare la parata del Milano Pride! Le sacche in nylon 34x43cm sono utilissime per portare con sè borracce d\'acqua, bandiere e tutto il necessario per essere favolosi! E i gadget non finiscono qui: Bracciale, Spille e Tira Lampo sono tutte incluse!')) ?>
            </div>
        </div>
        <div class="col-10 col-md-5 col-lg-3 reward">
            <div class="reward-box">
                <img src="<?= Url::resource('assets/imgs/tier2.png') ?>"
                     alt="<?= Html::escape(l("T-Shirt Polimi Pride")) ?>"/>
                <div class="reward-contribution">
                    <div class="contribution-label"><?= Html::escape(l('Contributo Minimo')); ?>:</div>
                    <div class="reward-amount"><?= DonationController::REWARD_THRESHOLD2 ?>&euro;</div>
                </div>
                <a href="<?= Url::to('donation', ['reward' => 2]) ?>"><?= Html::escape(l('Dona')); ?></a>
            </div>
            <div class="reward-description">
                <?= Html::escape(l("Non perderti la t-shirt PoliMi Pride! Insieme a tutti gli altri gadget completa perfettamente la collezione PoliMi Pride 2019")); ?>
            </div>
        </div>
    </div>
</section>
<section id="donate">
    <a href="<?= Url::to('donation') ?>" class="btn"><?= Html::escape(l('Ricevi i Gadget')); ?></a>
</section>
