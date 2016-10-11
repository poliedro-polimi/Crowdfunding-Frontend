<?php
namespace site\controllers;

use nigiri\Controller;

class SiteController extends Controller {
    public function actionHome(){
        return $this->renderView('site/home');
    }
}
