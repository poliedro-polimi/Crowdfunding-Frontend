<?php

use nigiri\Controller;
use nigiri\Site;
use \nigiri\views\Html;
use \nigiri\views\Url;

/** @var string $title */
/** @var string $head */
/** @var string $banner_img */
/** @var string $body */
/** @var string $script */
/** @var string $ready */

?>
<!doctype html>
<html lang="<?= Site::getRouter()->getRequestedLanguage() ?>">
<head>
    <title><?= Html::escape($title)?></title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="theme-color" content="#b0003a">
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Fira+Sans" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://poliedro-polimi.it/wp-content/themes/passivello/inc/css/passivello.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.8/css/all.css" integrity="sha384-3AB7yXWz4OeoZcPbieVW64vVXEwADiYyAEhwilzWsLw+9FgqpyjjStpPnpBO8o8S" crossorigin="anonymous">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css" />
    <link rel="stylesheet" href="<?= Url::resource('/assets/css/style.css') ?>">

    <?= $head; ?>
</head>
<body class="<?= Controller::camelCaseToUnderscore(Site::getRouter()->getControllerName())?> <?=
Controller::camelCaseToUnderscore(Site::getRouter()->getActionName()) ?>_action">
<nav id="mainNav" class="navbar navbar-default navbar-custom navbar-fixed-top">
    <div class="container">

        <div class="navbar-header page-scroll">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only"><?= l('Commuta navigazione') ?></span> Menu <i class="fa fa-bars"></i>
            </button>
            <a class="navbar-brand page-scroll" href="<?= Url::to('/') ?>"><?= page_include(dirname(dirname(__DIR__))
                .'/assets/imgs/polimipride_linear.svg') ?></a>
            <div class="navbar-lang">
                <p><?=Html::a(Url::to('', '', false, 'it'),'IT')?></p>
                <p><?=Html::a(Url::to('', '', false, 'en'),'EN')?></p>
            </div>
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <li class="hidden"><a href="#page-top"></a></li>
                <li><a class="extern" href="https://poliedro-polimi.it" id=""><?=l('Chi Siamo')?> <i class="fa fa-external-link"></i></a></li>
                <li><a class="extern" href="https://polimipride.it" id="">PoliMi Pride <i class="fa fa-external-link"></i></a></li>
                <li><a href="<?=Url::to('/')?>#rewards" id=""><?=l('Dona')?></a></li>
                <li><a href="https://poliedro-polimi.it#contatti" id="" class="extern"><?=l('Contatti')?> <i class="fa fa-external-link"></i></a></li>
            </ul>
        </div>
    </div>
</nav>

<section class="jumbotron" id="header">
    <img src="<?=$banner_img?>" id="banner_background" />
    <div class="container">
        <h1 class="section-title"><span id="countdown"><span class="days">00</span>d&nbsp;&nbsp;<span
                    class="hours">00</span>h&nbsp;&nbsp;<span class="minutes">00</span>m&nbsp;&nbsp;<span class="seconds">00</span>s</span></h1>
        <h2 class="section-content"><?=l('al PoliMi Pride')?></h2>
    </div>
</section>
<?=$body?>
<footer>
    <div class="container">
        <div class="row">
            <div class="col-xs-3 col-xs-offset-1" id="social-feed">
                <h3>#polimipride</h3>
            </div>
            <div class="col-xs-4 col-xs-offset-2" id="initiative-sign">
                <h2>PoliEdro x <strong>PoliMi</strong> Pride</h2>
                <div><?=l('PoliEdro dal 2012 vuole essere un punto di riferimento per tutti gli studenti del Politecnico di Milano, in particolar modo per gli studenti LGBTI+ (Lesbiche, Gay, Bisex, Trans, ...). Organizziamo eventi di carattere culturale (cineforum, conferenze), oltre ad eventi ricreativi, come divertenti serate tra amici nei locali di Milano. Dal 2017 organizza PoliMi Pride per portare il volto del Politecnico al Milano Pride.')?></div>
                <div id="contacts"><a href="https://www.facebook.com/poliedro.polimi" class="extern"><i
                            class="fab fa-facebook-f"></i></a><a href="https://www.instagram.com/poliedro.polimi" class="extern"><i
                            class="fab fa-instagram"></i></a><a href="https://t.me/PoliEdro" class="extern"><i class="fab fa-telegram-plane"></i></a></div>
            </div>
        </div>
        <div id="legal"><a href="<?=Url::to('site/legal')?>">Condizioni di Utilizzo e Privacy Policy</a></div>
    </div>
</footer>
<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="<?=Url::resource('/assets/js/site.js')?>"></script>

<?=$script?>

<?=$ready?>
</body>
</html>