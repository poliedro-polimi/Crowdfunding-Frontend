<?php
/** @var int $amount */

use nigiri\Site;
use nigiri\views\Url;

Site::getTheme()->append('<script src="https://www.paypalobjects.com/api/checkout.js"></script>', 'head');
Site::getTheme()->append('<script src="'.Url::resource('assets/js/payment.js').'" type="application/javascript"></script>', 'script');
?>
<div class="container">
<h1>Donazione</h1>

<section id="donation_amount">
    <div class="slider_objective" id="obj1"><img src="" /><div class="objective_arrow"></div></div>
    <div class="slider_objective" id="obj2"><img src="" /><div class="objective_arrow"></div></div>
    <div class="slider_objective" id="obj3"><img src="" /><div class="objective_arrow"></div></div>
    <div id="donation_slider">
        <div class="ui-slider-handle"><div id="handle-label-arrow"></div><div id="handle-label"><?= $amount
                ?>&euro;</div></div>
    </div>
</section>

<section id="donation_form">

</section>

<div id="pay-button"></div>
</div>

<?php
Site::getTheme()->append('

$("#donation_slider").slider({
    min: 0,
    max: 15,
    value: '.$amount.',
    slide: function(ev, ui){
        $("#handle-label").text(ui.value+"€");
    }
});

', 'script_on_ready');
