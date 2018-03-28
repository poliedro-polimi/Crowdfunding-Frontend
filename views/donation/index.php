<?php
/** @var int $amount */

use nigiri\Site;
use nigiri\views\Html;
use nigiri\views\Url;

Site::getTheme()->append('<script src="https://www.paypalobjects.com/api/checkout.js"></script>', 'head');
Site::getTheme()->append('<script src="'.Url::resource('assets/js/payment.js').'" type="application/javascript"></script>', 'script');

define('REWARD_THRESHOLD1', 2);
define('REWARD_THRESHOLD2', 5);
define('REWARD_THRESHOLD3', 10);
?>
<div class="container">
<h1><?= Html::escape(l('Donazioni')); ?></h1>

<section id="donation_amount">
    <div id="donation_objectives">
        <div class="donation_objective" id="obj1" data-threshold="<?= REWARD_THRESHOLD1 ?>"><img src="" /><div
              class="objective_arrow"></div></div>
        <div class="donation_objective" id="obj2" data-threshold="<?= REWARD_THRESHOLD2 ?>"><img src="" /><div
              class="objective_arrow"></div></div>
        <div class="donation_objective" id="obj3" data-threshold="<?= REWARD_THRESHOLD3 ?>"><img src="" /><div
              class="objective_arrow"></div></div>
    </div>
    <div id="donation_slider">
        <div class="ui-slider-handle"><div id="handle-label"><div id="handle-label-arrow"></div><span><?= $amount
                    ?>&euro;</span></div></div>
    </div>
</section>

<section id="donation_data">
    <div id="general_data">
        <div class="form-inline">
            <div class="form-group">
                <label for="nome" class="control-label"><?= l('Nome'); ?>:</label>
                <input type="text" name="nome" id="nome" class="form-control" />
            </div>
            <div class="form-group">
                <label for="cognome" class="control-label"><?= l('Cognome'); ?>:</label>
                <input type="text" name="cognome" id="cognome" class="form-control" />
            </div>
        </div>
        <div class="form-inline">
            <div class="form-group">
                <label for="email" class="control-label"><?= l('Email'); ?>:</label>
                <input type="email" name="email" id="email" class="form-control" />
            </div>
            <div class="form-group">
                <label for="email2" class="control-label"><?= l('Conferma Email'); ?>:</label>
                <input type="email" name="email2" id="email2" class="form-control" />
            </div>
        </div>
        <div class="form-group">
            <label for="tel" class="control-label"><?= l('Numero di Telefono') ?>:</label>
            <input type="tel" name="tel" id="tel" class="form-control">
            <div class="help-block"><?= Html::escape(l('Sarà utilizzato solo in caso di emergenza o se non riuscissimo a contattarti in nessun altro modo')) ?></div>
        </div>
        <div class="form-inline">
            <div class="form-group">
                <label for="amount" class="control-label"><?= l('Ammontare della Donazione'); ?>:</label>
                <div class="input-group">
                    <input type="number" name="amount" id="amount" class="form-control" value="<?= $amount ?>" />
                    <div class="input-group-addon">&euro;</div>
                </div>
            </div>
        </div>
    </div>
    <fieldset>
        <legend><?= l('Ricompensa') ?></legend>
        <p><?= Html::escape(l('Per ringraziarti della tua donazione a PoliEdro puoi scegliere tra i seguenti pacchetti
         di gadget')); ?>:</p>
        <div class="radio">
            <label>
                <input type="radio" name="chosenReward" id="reward0" value="0" checked="checked" />
                <?= Html::escape(l('Non desidero alcuna ricompensa')); ?>
            </label>
        </div>
        <div class="form-inline">
            <label class="radio-inline">
                <input type="radio" name="chosenReward" id="reward1" value="1" disabled="disabled"
                       data-threshold="<?= REWARD_THRESHOLD1 ?>" />
                <?= Html::escape(l('Adesivi e Spilla PoliMi Pride (donazione minima %s€)', REWARD_THRESHOLD1)); ?>
            </label>
            <div class="form-group qty">
                <label class="sr-only" for="qty1"><?= Html::escape(l('Quantità')); ?></label>
                <div class="input-group">
                    <div class="input-group-addon"><?= Html::escape(l('Qtà')) ?></div>
                    <input type="number" name="qty1" id="qty1" class="form-control" value="0" />
                </div>
            </div>
        </div>
        <div class="form-inline">
            <label class="radio-inline">
                <input type="radio" name="chosenReward" id="reward2" value="2" disabled="disabled"
                       data-threshold="<?= REWARD_THRESHOLD2 ?>" />
                <?= Html::escape(l('Adesivi, Spilla e Shopper PoliMi Pride (donazione minima %s€)',
                REWARD_THRESHOLD2)); ?>
            </label>
            <div class="form-group qty">
                <label class="sr-only" for="qty2"><?= Html::escape(l('Quantità')); ?></label>
                <div class="input-group">
                    <div class="input-group-addon"><?= Html::escape(l('Qtà')) ?></div>
                    <input type="number" name="qty2" id="qty2" class="form-control" value="0" />
                </div>
            </div>
        </div>
        <div class="form-inline">
            <label class="radio-inline">
                <input type="radio" name="chosenReward" id="reward3" value="3" disabled="disabled" data-threshold="<?= REWARD_THRESHOLD3 ?>" />
                <?= Html::escape(l('Kit PoliMi Pride Completo: Adesivi, Spilla, Shopper e T-Shirt (donazione minima %s€)', REWARD_THRESHOLD3)); ?>
            </label>
            <div class="form-group qty">
                <label class="sr-only" for="qty3"><?= Html::escape(l('Quantità')); ?></label>
                <div class="input-group">
                    <div class="input-group-addon"><?= Html::escape(l('Qtà')) ?></div>
                    <input type="number" name="qty3" id="qty3" class="form-control" value="0" />
                </div>
            </div>
        </div>
        <fieldset id="tshirt_data">
            <legend><?= Html::escape(l('Scegli la tua T-Shirt')); ?></legend>
            <div class="tshirt_chooser col-xs-3">
                <div class="radio">
                    <label>
                        <input type="radio" name="shirt-type" value="t-shirt" />
                        <?= Html::escape(l('T-shirt classica')); ?><br /><img src="https://ae01.alicdn
                        .com/kf/HTB1FnwySFXXXXa0XVXXq6xXFXXXo/Men-s-Leisure-Rainbow-Sheep-Of-The-Family-LGBT-T-shirt-White-Short-Sleeve-Custom-T.jpg_640x640.jpg" />
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="shirt-type" value="tank-top" />
                        <?= Html::escape(l('Canotta')); ?><br /><img src="https://www.pianetaoutlet.it/59072-large_default/adidas-trefoil-tank-canotta-uomo-nera.jpg" />
                    </label>
                </div>
                <div class="form-group">
                    <label for="shirt-size"><?= Html::escape(l('Scegli la taglia')); ?>:</label>
                    <select class="form-control" name="shirt-size" id="shirt-size">
                        <option value="XS">XS</option>
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                        <option value="XXL">XXL</option>
                    </select>
                </div>
            </div>
        </fieldset>
        <fieldset id="location_data">
            <legend><?= Html::escape(l('Indica dove preferisci ritirare le tue ricompense')); ?></legend>
            <p><?= Html::escape(l('Per ragioni organizzative non possiamo spedire gli oggetti a un tuo domicilio, li
                consegneremo invece personalmente presso i nostri stand nelle sedi del Politecnico organizzati
                durante il mese di Giugno')); ?></p>
            <div class="radio">
                <label><input type="radio" name="location" value="leonardo" />Leonardo</label>
            </div>
            <div class="radio">
                <label><input type="radio" name="location" value="bovisa" />Bovisa</label>
            </div>
        </fieldset>
    </fieldset>
    <div class="form-group">
        <label for="notes"><?= Html::escape(l('Note Addizionali')) ?></label>
        <textarea class="form-control" rows="3" id="notes"></textarea>
    </div>
    <div id="pay-button"></div>
</section>
</div>
<script type="application/javascript">
    //Variables to be referenced in payment.js
    var initialAmount = <?= $amount ?>;
    var payPalLocale = <?php
    switch(Site::getRouter()->getRequestedLanguage()){
        case 'it':
            echo json_encode('it_IT');
            break;
        case 'en':
            echo json_encode('en_GB');
            break;
    }
    ?>;
</script>