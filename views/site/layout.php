<?php

use nigiri\Controller;
use nigiri\Site;
use \nigiri\views\Html;
use \nigiri\views\Url;

/** @var string $title */
/** @var string $head */
/** @var string $body */
/** @var string $script */
/** @var string $ready */

?>
<!doctype html>
<html lang="<?= Site::getRouter()->getRequestedLanguage() ?>">
<head>
    <title><?= Html::escape($title) ?></title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="<?= Url::resource('assets/imgs/favicon.ico'); ?>" />
    <link rel="apple-touch-icon" href="<?= Url::resource('assets/imgs/favicon.png') ?>">
    <meta name="theme-color" content="#ffffff">
    <link href="https://fonts.googleapis.com/css?family=Fira+Sans" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.8/css/all.css"
          integrity="sha384-3AB7yXWz4OeoZcPbieVW64vVXEwADiYyAEhwilzWsLw+9FgqpyjjStpPnpBO8o8S" crossorigin="anonymous">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css"/>
    <link rel="stylesheet" href="<?= Url::resource('/assets/css/style.css') ?>">

    <?= $head; ?>
</head>
<body class="<?= Controller::camelCaseToUnderscore(Site::getRouter()->getControllerName()) ?> <?=
Controller::camelCaseToUnderscore(Site::getRouter()->getActionName()) ?>_action">


<nav id="mainNav"
     class="navbar navbar-light navbar-expand-lg navbar-custom fixed-top justify-content-between affix transparent">
    <div class="container container-navbar">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header page-scroll">
            <a class="navbar-brand page-scroll" href="https://polimipride.it">
                <?= page_include(dirname(dirname(__DIR__)) . '/assets/imgs/polimipride_linear.svg') ?>
            </a>

            <div class="navbar-lang">
                <p><?= Html::a(Url::to('', '', false, 'it'), 'IT') ?></p>
                <p><?= Html::a(Url::to('', '', false, 'en'), 'EN') ?></p>
            </div>
        </div>

        <button class="navbar-toggler" type="button" data-toggle="collapse"
                data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            <span class="navbar-red-bullet">&bullet;</span>
        </button>
        <!-- Collect the nav links, forms, and other content for toggling -->

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav nav ml-auto mt-2 mt-lg-0">


                <li class="nav-item">
                    <a class="page-scroll nav-link" href="https://polimipride.it/<?= Site::getRouter()->getRequestedLanguage() ?>/#project"><?= Html::escape(l('L\'Iniziativa'))
                        ?></a>
                </li>
                <li class="nav-item">
                    <a class="page-scroll nav-link" href="https://polimipride.it/<?= Site::getRouter()->getRequestedLanguage() ?>/#about"><?= Html::escape(l('Chi Siamo')) ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                       href="https://poliedro-polimi.it/<?= Site::getRouter()->getRequestedLanguage() ?>/#contatti"><?= Html::escape(l('Contatti')) ?></a>
                </li>
                <li class="nav-item nav-important">
                    <a class="page-scroll nav-link" href="<?= Url::to('/') ?>"><?= Html::escape(l('Sostienici Ora')) ?>
                    </a>
                </li>

            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container-fluid -->
</nav>

<section class="jumbotron" id="header">
    <div class="container">
        <h1 class="section-title"><span id="countdown"><span class="days">00</span>d&nbsp;&nbsp;<span
                        class="hours">00</span>h&nbsp;&nbsp;<span class="minutes">00</span>m&nbsp;&nbsp;<span
                        class="seconds">00</span>s</span></h1>
        <h2 class="section-content"><?= l('alla Pride Week') ?></h2>
    </div>
</section>
<div id="page_content">
<?= $body ?>
</div>

<footer class="site-footer">
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-6 col-poliedro-logo">
                <?= page_include(dirname(dirname(__DIR__)).'/assets/imgs/footer_logos.svg') ?>
            </div>
            <div class="col-12 col-md-6 col-details d-flex align-items-end">
                <div class="align-middle">
                    <p class="poliedro-x-polimipride section-title">
                        <?= page_include(dirname(dirname(__DIR__)) . '/assets/imgs/poliedro.svg') ?>
                        ✕&nbsp;
                        <?= page_include(dirname(dirname(__DIR__)) . '/assets/imgs/polimipride_linear_nologo.svg') ?>
                    </p>
                    <p>
                        <?= l('L\'iniziativa <strong>PoliMi Pride</strong> è realizzata con il contributo del
                        <a href="https://polimi.it" rel="noopener" target="_blank">Politecnico di Milano</a>.') ?>
                    </p>
                    <p>
                        <?= l('Visita il nostro <a href="https://poliedro-polimi.it">sito</a> per scoprire tutte le
                        altre nostre attività.') ?>
                    </p>
                    <p class="section-social">
                        <a href="https://poliedro-polimi.it/it/#contatti"><i class="fas fa-at"></i></a>
                        |
                        <a href="https://facebook.com/poliedro.polimi/" target="_blank" rel="noopener"><i
                                    class="fab fa-facebook-f"></i></a>
                        |
                        <a href="https://instagram.com/poliedro.polimi/" target="_blank" rel="noopener"><i
                                    class="fab fa-instagram"></i></a>
                        |
                        <a href="https://t.me/PoliEdroLive" target="_blank" rel="noopener"><i
                                    class="fab fa-telegram-plane"></i></a>
                    </p>
                </div>
            </div>
        </div>
        <div class="section-copyright">
            &copy; PoliEdro 2018 | <a
                    href="https://poliedro-polimi.it/it/<?= l('crediti') ?>/"><?= Html::escape(l('Crediti')) ?></a>
        </div>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.2.1.min.js"
        integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
        integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="<?= Url::resource('/assets/js/site.js') ?>"></script>

<?= $script ?>

<?= $ready ?>
</body>
</html>
