<?php
/** @var int $amount */

/** @var int $reward */

use nigiri\Site;
use nigiri\views\Html;
use nigiri\views\Url;
use \site\controllers\DonationController;

Site::getTheme()->append('<script src="https://www.paypalobjects.com/api/checkout.js"></script>', 'head');
Site::getTheme()->append('<script src="' . Url::resource('assets/js/payment.js') . '" type="application/javascript"></script>',
  'script');
Site::getTheme()->resetPart("banner_img");
Site::getTheme()->append(Url::resource("assets/imgs/banner2.png"), 'banner_img');

?>
<div class="container">
    <h1><?= Html::escape(l('Donazioni')); ?></h1>

    <section id="donation_amount">
        <div id="donation_objectives">
            <div class="donation_objective" id="obj1" data-threshold="<?= DonationController::REWARD_THRESHOLD1 ?>"><img
                  src="<?= Url::resource('assets/imgs/tier1.png') ?>" class="item"/>
                <div
                  class="objective_arrow"></div>
            </div>
            <div class="donation_objective" id="obj2" data-threshold="<?= DonationController::REWARD_THRESHOLD2 ?>"><img
                  src="<?= Url::resource('assets/imgs/tier2.png') ?>" class="item"/>
                <div
                  class="objective_arrow"></div>
            </div>
        </div>
        <div id="donation_slider">
            <div class="ui-slider-handle">
                <div id="handle-label">
                    <div id="handle-label-arrow"></div>
                    <span><?= $amount
                        ?>&euro;</span></div>
            </div>
        </div>
    </section>
    <section id="error_box" class="text-danger"></section>
    <div id="form_note" class="text-muted"><?= nl2br(Html::escape(l("Siamo a conoscenza di problemi nei pagamenti utilizzando gli iPhone.
Se non riuscite a fare la donazione provate da pc o da dispositivi diversi dagli iPhone e iPad. Ci scusiamo per l'inconveniente.")))?></div>
    <section id="donation_data">
        <form novalidate>
            <div id="required_legend"><?= Html::escape(l("I campi segnati con * sono obbligatori")); ?></div>
            <div id="general_data">
                <div class="form-row justify-content-around">
                    <div class="form-group col-xs-10 col-sm-6">
                        <label for="nome" class="control-label"><?= Html::escape(l('Nome')); ?>:</label>
                        <input type="text" name="nome" id="nome" class="form-control"/>
                    </div>
                    <div class="form-group col-xs-10 col-sm-6">
                        <label for="cognome" class="control-label"><?= Html::escape(l('Cognome')); ?>:</label>
                        <input type="text" name="cognome" id="cognome" class="form-control"/>
                    </div>
                </div>
                <div class="form-row justify-content-around">
                    <div class="form-group col-xs-10 col-sm-6">
                        <label for="email" class="control-label"><?= Html::escape(l('Email')); ?>:</label>
                        <input type="email" name="email" id="email" class="form-control"/>
                        <small class="form-text text-muted"><?= Html::escape(l('Sarà utilizzata per tutte le comunicazioni sulla tua 
                    donazione')); ?></small>
                    </div>
                    <div class="form-group col-xs-10 col-sm-6">
                        <label for="tel" class="control-label"><?= Html::escape(l('Numero di Telefono')); ?>:</label>
                        <input type="tel" name="tel" id="tel" class="form-control">
                        <small
                          class="form-text text-muted"><?= Html::escape(l('Sarà utilizzato solo in caso non riuscissimo a contattarti per email')) ?></small>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-auto">
                        <label for="amount" class="control-label"><?= Html::escape(l('Ammontare della Donazione')); ?><span
                              class="required">&nbsp;*</span>:</label>
                        <div class="input-group">
                            <input type="number" name="amount" id="amount" class="form-control" value="<?= $amount ?>"
                                   required />
                            <div class="input-group-append">
                                <div class="input-group-text">&euro;</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <fieldset>
                <legend><?= l('Ricompensa') ?></legend>
                <p><?= Html::escape(l('Per ringraziarti della tua donazione a PoliEdro puoi scegliere tra i seguenti pacchetti
             di gadget')); ?></p>

                <div class="form-check reward-line">
                    <input type="radio" name="chosenReward" id="reward0" value="0" class="form-check-input"
                      <?= (empty($reward) ? 'checked="checked"' : '') ?> />
                    <label class="form-check-label" for="reward0">
                        <?= Html::escape(l('Non desidero alcuna ricompensa')); ?>
                    </label>
                </div>
                <div class="form-inline reward-line">
                    <div class="form-check">
                        <input type="radio" name="chosenReward" id="reward1" value="1" disabled="disabled"
                               class="form-check-input"
                               data-threshold="<?= DonationController::REWARD_THRESHOLD1 ?>"
                          <?= ($reward == 1 ? 'checked="checked"' : '') ?> />
                        <label class="form-check-label" for="reward1">
                            <?= Html::escape(l('Sacca zainetto, Spille, Bracciale e Tira Lampo (donazione minima %s€)',
                              DonationController::REWARD_THRESHOLD1)); ?>
                        </label>
                    </div>
                    <div class="form-group qty">
                        <label class="sr-only" for="qty1"><?= Html::escape(l('Quantità')); ?></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><?= Html::escape(l('Q.tà')) ?></div>
                            </div>
                            <input type="number" name="qty1" id="qty1" disabled="disabled" class="form-control" value="0"
                                   min="0"/>
                        </div>
                    </div>
                    <small class="form-text text-muted"><?= Html::escape(l('Sacca Zainetto in Nylon, decorata con il 
                    nuovo logo PoliMi Pride, 43x34cm. Nuove spille, un Tira Lampo per decorare gli zaini e il bracciale da portare sempre con te!')) ?></small>
                </div>
                <div class="form-inline reward-line">
                    <div class="form-check">
                        <input type="radio" name="chosenReward" id="reward2" value="2" disabled="disabled"
                               class="form-check-input"
                               data-threshold="<?= DonationController::REWARD_THRESHOLD2 ?>"
                          <?= ($reward == 3 ? 'checked="checked"' : '') ?> />
                        <label class="form-check-label" for="reward2">
                            <?= Html::escape(l('Kit PoliMi Pride Completo: Spille, Sacca, Tira Lampo, Bracciale e T-Shirt (donazione minima %s€)',
                              DonationController::REWARD_THRESHOLD2)); ?>
                        </label>
                    </div>
                    <div class="form-group qty">
                        <label class="sr-only" for="qty2"><?= Html::escape(l('Quantità')); ?></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><?= Html::escape(l('Q.tà')) ?></div>
                            </div>
                            <input type="number" name="qty2" id="qty2" class="form-control" disabled="disabled" value="0"
                                   min="0"/>
                        </div>
                    </div>
                    <small class="form-text text-muted"><?= Html::escape(l('Maglietta di cotone con stampa
                        digitale del nuovo logo PoliMi Pride. Disponibile in 6 taglie, sia come t-shirt che come
                        canottiera.')) ?><br />
                    </small>
                </div>
                <small
                  class="help-block"><?= Html::escape(l('Puoi scegliere quantità maggiori delle ricompense con una donazione almeno pari a un multiplo della donazione minima necessaria per il pacchetto che hai scelto')); ?></small>
                <fieldset id="tshirt_data">
                    <legend><?= Html::escape(l('Scegli la tua T-Shirt')); ?></legend>
                    <div class="form-row justify-content-around">
                        <div class="tshirt_chooser col-xs-10 col-sm-5 col-md-4 col-lg-3">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="shirt-type" value="t-shirt"/>
                                    <?= Html::escape(l('T-shirt classica')); ?><br/><img
                                      src="<?= Url::resource('assets/imgs/tshirt.png') ?>"/>
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="shirt-type" value="tank-top"/>
                                    <?= Html::escape(l('Canotta')); ?><br/><img
                                      src="<?= Url::resource('assets/imgs/canotta.png') ?>"/>
                                </label>
                            </div>
                            <div class="form-group">
                                <label for="shirt-size"><?= Html::escape(l('Scegli la taglia')); ?><span
                                      class="required">&nbsp;*</span>:</label>
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
                    </div>
                    <table class="table">
                        <thead>
                        <tr>
                            <td>Approx. 3%</td>
                            <th scope="col">XS</th>
                            <th scope="col">S</th>
                            <th scope="col">M</th>
                            <th scope="col">L</th>
                            <th scope="col">XL</th>
                            <th scope="col">XXL</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th scope="row">Larghezza (cm)</th>
                            <td>48</td>
                            <td>51</td>
                            <td>53.5</td>
                            <td>56</td>
                            <td>58</td>
                            <td>61</td>
                        </tr>
                        <tr>
                            <th scope="row">Lunghezza (cm)</th>
                            <td>68</td>
                            <td>70</td>
                            <td>72</td>
                            <td>74</td>
                            <td>76</td>
                            <td>78</td>
                        </tr>
                        </tbody>
                    </table>
                </fieldset>
                <fieldset id="location_data">
                    <legend><?= Html::escape(l('Indica dove preferisci ritirare le tue ricompense')); ?></legend>
                    <p><?= Html::escape(l('Per ragioni organizzative non possiamo spedire gli oggetti al tuo domicilio, li
                    consegneremo invece personalmente presso i nostri stand nelle sedi del Politecnico organizzati
                    durante il mese di Giugno')); ?><span class="required">&nbsp;*</span></p>
                    <div class="radio">
                        <label><input type="radio" name="location" value="leonardo"/>Leonardo</label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" name="location" value="bovisa"/>Bovisa</label>
                    </div>
                </fieldset>

                <!--p class="text-info"><?= nl2br(Html::escape(l("Ci spiace ma le prenotazioni dei gadget sono chiuse!\nPassa ai nostri stand al Politecnico dal 24 al 28 Giugno, avremo qualche gadget in più per chi non ha potuto prenotarli!\nPuoi comunque fare una donazione a favore delle attività di PoliEdro se lo desideri."))); ?></p>

                <input type="radio" name="chosenReward" id="reward0" value="0" class="form-check-input" style="display: none;"
                    checked="checked" /-->
            </fieldset>
            <div class="form-group">
                <label for="notes"><?= Html::escape(l('Note Addizionali')) ?></label>
                <textarea class="form-control" rows="3" id="notes"></textarea>
            </div>
            <div id="pay-button"></div>
        </form>
    </section>
    <p class="text-muted"><?= Html::escape(l('Hai già fatto una donazione o hai problemi tecnici?')); ?> <a
          href="https://poliedro-polimi.it#contatti" class="extern"><?= Html::escape(l('Contattaci')) ?><i class="fa
          fa-external-link"></i></a></p>
</div>
<script type="application/javascript">
    //Variables to be referenced in payment.js
    var initialAmount = <?= $amount ?>;
    var backendLang = <?= json_encode(Site::getRouter()->getRequestedLanguage()); ?>;
    var payPalLocale = <?php
      switch (Site::getRouter()->getRequestedLanguage()) {
          case 'it':
              echo json_encode('it_IT');
              break;
          case 'en':
              echo json_encode('en_GB');
              break;
      }
      ?>;
    var confirm_url = <?= json_encode(Url::to("donation/confirmation")); ?>;

    var validation_error_msg = <?= json_encode(l('Verifica i dati inseriti!')); ?>;
    var paypal_error_msg = <?= json_encode(l('Si è verificato un errore in PayPal. Riprova')); ?>;
    var app_error_msg = <?= json_encode(l('Si è verificato un errore nel nostro sistema. Riprova')) ?>;
    var instrument_declined_msg = <?= json_encode(l('Il tuo metodo di pagamento è stato rifiutato dal gestore o dalla banca, oppure non può essere utilizzato per questo tipo di pagamento. Controlla di avere abbastanza disponibilità e che i dati siano validi.')) ?>;
    var donation_id_msg = <?= json_encode(l('Per problemi puoi contattarci indicando il tuo ID di pagamento: ')) ?>;
</script>
