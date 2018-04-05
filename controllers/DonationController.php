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
    const REWARD_THRESHOLD1 = 4;
    const REWARD_THRESHOLD2 = 8;
    const REWARD_THRESHOLD3 = 12;

    public function actionIndex(){
        $amount = empty($_GET['amount'])?0:(int)$_GET['amount'];
        return self::renderView("donation/index", ['amount' => $amount]);
    }

    public function actionConfirmation() {
        return self::renderView("donation/confirmation", [
          'donation_id' => $_GET['donation'],// !empty($_GET['donation'])?$_GET['donation']:'',
          'reward' => empty($_GET['reward'])?false:(bool)$_GET['reward'],
          'mail_fail' => empty($_GET['mail_fail'])?false:(bool)$_GET['mail_fail']
        ]);
    }
}
