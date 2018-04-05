<?php
use nigiri\views\Url;
use nigiri\views\Html;
use site\controllers\DonationController;
?>
<section id="how_to_help">
    <div class="container">
        <h1 class="section-title"><?= l('Come puoi aiutarci?') ?><br />-</h1>
        <div class="section-content"><?= Html::escape(l('Quest\'anno l\'organizzazione dell\'evento, prevede la consegna
            di alcuni gadget targati "Polimi Pride" in seguito alla donazione di un contributo minimo
            volontario.')) ?></div>
    </div>
</section>
<section id="sequence">
    <div class="row">
        <div class="col-xs-2 col-xs-offset-2"><div id="siteurl">www.polimipride.it</div></div>
        <div class="col-xs-1 arrow"><?= page_include(dirname(dirname(__DIR__)).'/assets/imgs/freccia.svg') ?></div>
        <div class="col-xs-2"><?= page_include(dirname(dirname(__DIR__)).'/assets/imgs/donate_in.svg') ?></div>
        <div class="col-xs-1 arrow"><?= page_include(dirname(dirname(__DIR__)).'/assets/imgs/freccia.svg') ?></div>
        <div class="col-xs-2"><?= page_include(dirname(dirname(__DIR__)).'/assets/imgs/donate_out.svg') ?></div>
    </div>
</section>
<section id="rewards">
    <h1><?= Html::escape(l('Ricompense')); ?></h1>
    <div class="row">
        <div class="col-xs-10 col-xs-offset-1 col-sm-3 col-sm-offset-1 col-md-2 col-md-offset-2 reward">
            <div class="reward-box">
                <img src="https://www.muscaspromo.com/image/cache/catalog/Shopper/sp15145-borse-in-cotone-colorate-130-grmq-sp15145-borse-in-cotone-colorate-130-grmq-1200x900.jpg" alt="Spille e Adesivi PoliMi Pride" />
                <div class="reward-contribution">
                    <div class="contribution-label"><?= l('Contributo Minimo Volontario') ?>:</div>
                    <div class="reward-amount"><?= DonationController::REWARD_THRESHOLD1 ?>&euro;</div>
                </div>
                <a href="<?= Url::to('donation', ['amount'=> DonationController::REWARD_THRESHOLD1]) ?>"><?= l('Ricevi') ?></a>
            </div>
            <div class="reward-description">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse id libero pretium, accumsan justo sit amet, euismod quam. Nullam et malesuada sem. Etiam vel urna ut diam pretium pulvinar ut et nibh. Ut diam ligula, sodales at mauris at, auctor feugiat lacus.
            </div>
        </div>
        <div class="col-xs-10 col-xs-offset-1 col-sm-3 col-sm-offset-1 col-md-2 col-md-offset-1 reward">
            <div class="reward-box">
                <img src="https://www.muscaspromo.com/image/cache/catalog/Shopper/sp15145-borse-in-cotone-colorate-130-grmq-sp15145-borse-in-cotone-colorate-130-grmq-1200x900.jpg" alt="Spille, Adesivi e Shopper PoliMi Pride" />
                <div class="reward-contribution">
                    <div class="contribution-label"><?= l('Contributo Minimo Volontario') ?>:</div>
                    <div class="reward-amount"><?= DonationController::REWARD_THRESHOLD2 ?>&euro;</div>
                </div>
                <a href="<?= Url::to('donation', ['amount'=> DonationController::REWARD_THRESHOLD2]) ?>"><?= l('Ricevi') ?></a>
            </div>
            <div class="reward-description">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse id libero pretium, accumsan justo sit amet, euismod quam. Nullam et malesuada sem. Etiam vel urna ut diam pretium pulvinar ut et nibh. Ut diam ligula, sodales at mauris at, auctor feugiat lacus.
            </div>
        </div>
        <div class="col-xs-10 col-xs-offset-1 col-sm-3 col-sm-offset-1 col-md-2 col-md-offset-1 reward">
            <div class="reward-box">
                <img src="https://www.muscaspromo.com/image/cache/catalog/Shopper/sp15145-borse-in-cotone-colorate-130-grmq-sp15145-borse-in-cotone-colorate-130-grmq-1200x900.jpg" alt="Kit Completo PoliMi Pride" />
                <div class="reward-contribution">
                    <div class="contribution-label"><?= l('Contributo Minimo Volontario') ?>:</div>
                    <div class="reward-amount"><?= DonationController::REWARD_THRESHOLD3 ?>&euro;</div>
                </div>
                <a href="<?= Url::to('donation', ['amount' => DonationController::REWARD_THRESHOLD3]) ?>"><?= l('Ricevi') ?></a>
            </div>
            <div class="reward-description">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse id libero pretium, accumsan justo sit amet, euismod quam. Nullam et malesuada sem. Etiam vel urna ut diam pretium pulvinar ut et nibh. Ut diam ligula, sodales at mauris at, auctor feugiat lacus.
            </div>
        </div>
    </div>
</section>
<section id="donate">
    <a href="<?= Url::to('donation') ?>" class="btn"><?= l('Dona'); ?></a>
</section>
