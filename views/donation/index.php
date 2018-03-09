<?php

use nigiri\Site;
use nigiri\views\Url;

Site::getTheme()->append('<script src="https://www.paypalobjects.com/api/checkout.js"></script>', 'head');
Site::getTheme()->append('<script src="'.Url::resource('assets/js/payment.js').'" type="application/javascript"></script>', 'script');
?>
<h1>BOH</h1>

<div id="pay-button"></div>
