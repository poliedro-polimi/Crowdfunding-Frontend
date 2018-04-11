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
    const REWARD_THRESHOLD2 = 6;
    const REWARD_THRESHOLD3 = 12;

    public function actionIndex()
    {
        $reward = empty($_GET['reward']) ? 0 : (int)$_GET['reward'];

        switch ($reward){
            case 1:
                $amount = self::REWARD_THRESHOLD1;
                break;
            case 2:
                $amount = self::REWARD_THRESHOLD2;
                break;
            case 3;
                $amount = self::REWARD_THRESHOLD3;
                break;
            default:
                $amount = 0;
        }

        return self::renderView("donation/index", [
          'amount' => $amount,
          'reward' => $reward
        ]);
    }

    public function actionConfirmation()
    {
        return self::renderView("donation/confirmation", [
          'donation_id' => $_GET['donation'],// !empty($_GET['donation'])?$_GET['donation']:'',
          'reward' => empty($_GET['reward']) ? false : (bool)$_GET['reward'],
          'mail_fail' => empty($_GET['mail_fail']) ? false : (bool)$_GET['mail_fail']
        ]);
    }
}
