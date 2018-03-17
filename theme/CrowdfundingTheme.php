<?php

namespace site\theme;

use nigiri\Controller;
use nigiri\Site;
use nigiri\themes\Theme;
use nigiri\views\Html;
use nigiri\views\Url;

class CrowdfundingTheme extends Theme {

    public function render()
    {
        $this->title .= (empty($this->title)?'':' - ').Site::getParam('site_name');

        $ready = '';
        if(!empty($this->script_on_ready)){
            $ready = <<<READY
<script type="application/javascript">
$(function(){
    {$this->script_on_ready}
});
</script>
READY;
        }

        echo '<!doctype html>
<html lang="'.Site::getRouter()->getRequestedLanguage().'">
<head>
<title>'.Html::escape($this->title).'</title>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="theme-color" content="#b0003a">
<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Fira+Sans" rel="stylesheet">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="https://poliedro-polimi.it/wp-content/themes/passivello/inc/css/passivello.css" crossorigin="anonymous">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
<link rel="stylesheet" href="'.Url::resource('/assets/css/style.css').'">

'.$this->head.'
</head>
<body class="'.Controller::camelCaseToUnderscore(Site::getRouter()->getControllerName()).' '
          .Controller::camelCaseToUnderscore(Site::getRouter()->getActionName()).'_action">
    <nav id="mainNav" class="navbar navbar-default navbar-custom navbar-fixed-top">
        <div class="container">

            <div class="navbar-header page-scroll">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Commuta navigazione</span> Menu <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand page-scroll" href="https://poliedro-polimi.it">'.page_include(dirname(__DIR__).'/assets/imgs/poliedro.svg').'</a>
                <div class="navbar-lang">
                    <p>'.Html::a(Url::to('', '', false, 'it'),'IT').'</p>
                    <p>'.Html::a(Url::to('', '', false, 'en'),'EN').'</p>
                </div>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li class="hidden"><a href="#page-top"></a></li>
                    <li><a class="page-scroll" href="https://polimipride.it" id="">PoliMi Pride <i class="fa fa-external-link"></i></a></li>
                    <li><a class="page-scroll" href="https://poliedro-polimi.it" id="">PoliEdro Home <i class="fa fa-external-link"></i></a></li>
                </ul>
            </div>
        </div>
    </nav>
'.$this->body.'

<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="'.Url::resource('/assets/js/navbar.js').'"></script>

'.$this->script.'

'.$ready.'
</body>
</html>';
    }
}
