<?php

use nigiri\Site;
use nigiri\views\Url;

Site::getTheme()->append('<link href="'.Url::resource('assets/css/jquery.socialfeed.css').'" rel="stylesheet" type="text/css">', 'head');

Site::getTheme()->append('<script type="application/javascript" src="'.Url::resource('assets/js/codebird.js').'"></script>', 'script');
Site::getTheme()->append('<script type="application/javascript" src="'.Url::resource('assets/js/doT.min.js').'"></script>', 'script');
Site::getTheme()->append('<script type="application/javascript" src="'.Url::resource('assets/js/moment.min.js').'"></script>', 'script');

if(Site::getRouter()->getRequestedLanguage()=='it') {
    Site::getTheme()->append('<script type="application/javascript" src="' . Url::resource('assets/js/moment-it.js') . '"></script>','script');
}
Site::getTheme()->append('<script type="application/javascript" src="'.Url::resource('assets/js/jquery.socialfeed.js').'"></script>', 'script');
?>
<div id="social-feed-content"></div>

<?php
Site::getTheme()->append('

$("#social-feed-content").socialfeed({
    instagram:{
        accounts: [\'#polimipride\', \'#polimi-pride\', \'#polimi_pride\'],  //Array: Specify a list of accounts from which to pull posts
        client_id: \'1cb3a2c0d304475b9a40424851fc5df9\',       //String: Instagram client id (option if using access token)
        access_token: \'4979368905.1cb3a2c.5b51b504fcd54007a0fec776d518dad8\' //String: Instagram access token
    },
    
    // FACEBOOK
    facebook:{
        accounts: [\'#polimipride\',\'#polimi_pride\', \'#polimi-pride\'],  //Array: Specify a list of accounts from which to pull wall posts
        access_token: \'87f1fe1dd2d78309c001cde9fa91d5c7\'
    },
    length: 200,
    show_media: false,
    template_html: "<article class=\\"social-feed-element\\"> <h4>{{=it.author_name}}</h4><p>{{it.text}}</p><a href=\\"{{it.link}}\\" target=\\"_blank\\">more</a></article>"
});

', 'script_on_ready');
