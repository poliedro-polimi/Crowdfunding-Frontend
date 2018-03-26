<?php
/**
 * @author Stefano Campanella
 * Date: 04/03/18
 * Time: 22.20
 */

namespace site\controllers;


use nigiri\Controller;

class DonationController extends Controller
{
    public function actionIndex(){
        $amount = empty($_GET['amount'])?0:(int)$_GET['amount'];
        return self::renderView("donation/index", ['amount' => $amount]);
    }
}
