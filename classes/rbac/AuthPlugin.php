<?php
namespace nigiri\rbac;

use nigiri\exceptions\Exception;
use nigiri\exceptions\Forbidden;
use nigiri\models\Permission;
use nigiri\models\Role;
use nigiri\plugins\PluginInterface;
use nigiri\Site;

class AuthPlugin implements PluginInterface{

    const DENY = -1;
    const ALLOW = 1;
    const AUTHENTICATED_USER = 2;

    private $config;

    /**
     * @var int|Role|Permission the default permission to apply when there is no specific policy defined for the current action
     */
    private $policy = null;

    public function __construct($config)
    {
        $this->config = $config;

        if(!empty($config['policy'])){
            $this->policy = $this->policyEvaluation($config['policy']);
        }
        else{
            $this->policy = self::DENY;
        }
    }

    public function beforeAction($actionName)
    {
        $policy = $this->policy;
        $allow = true;
        if(!empty($this->config['rules'])){
            $raw_p = null;
            foreach($this->config['rules'] as $rule){
                if(in_array($this->actionCamelToUnderscore($actionName), $rule['actions'])){
                    $raw_p = $rule;
                    break;
                }
            }

            if(!empty($raw_p)) {
                $allow = empty($raw_p['allow']) ? true : $raw_p['allow'];
                $policy = $this->policyEvaluation($raw_p['policy']);
            }
        }

        $this->applyPolicy($policy, $allow);
    }

    public function afterAction($actionName, $actionOutput)
    {
        return $actionOutput;
    }

    /**
     * @param $p
     * @param bool $isAllow tells if the current policy is an Allow or a Deny one. Deny Policies trigger HTTP403 when a
     * Permission or Role matches correctly, Allow Policies trigger HTTP403 when none matches
     * @throws Forbidden
     */
    private function applyPolicy($p, $isAllow = true){
        if($p == self::ALLOW){
            return;
        }
        elseif($p == self::DENY){
            throw new Forbidden();
        }
        elseif(is_array($p)){
            $match = false;
            foreach($p as $temp){
                if(is_int($temp) && $temp == self::AUTHENTICATED_USER){
                    if(Site::getAuth()->isLoggedIn()){
                        $match = true;
                        break;
                    }
                }
                elseif($temp instanceof Permission){
                    if(Site::getAuth()->userCan(Site::getAuth()->getLoggedInUser()->getId(), $p)){
                        $match = true;
                        break;
                    }
                }
                elseif($temp instanceof Role){
                    if(Site::getAuth()->userHasRole(Site::getAuth()->getLoggedInUser()->getId(), $p)){
                        $match = true;
                        break;
                    }
                }
            }

            if(($match && $isAllow) || (!$match && !$isAllow)){
                return;
            }

            throw new Forbidden();
        }
    }

    private function policyEvaluation($p){
        if(!empty($p)){
            if($p == self::ALLOW || $p == self::DENY){
                return $p;
            }
            else{
                if(!is_array($p)){
                    $p = [$p];
                }

                $out = [];
                foreach($p as $temp){
                    if(is_string($temp)){
                        try{
                            $out[] = new Permission($p);
                        }
                        catch(Exception $e){//It's not a valid permission name
                            $r = Site::getAuth()->getRole($p);
                            if(!empty($r)){
                                $out[] = $r;
                            }
                        }
                    }
                    elseif(is_int($temp) && $p == self::AUTHENTICATED_USER){
                        $out[] = self::AUTHENTICATED_USER;
                    }
                }

                if(empty($out)){
                    return self::DENY;
                }
                else{
                    return $out;
                }
            }
        }
        else{
            return self::DENY;
        }
    }

    private function actionCamelToUnderscore($action){
        if(strpos($action, 'action')===0){
            $action = substr($action, 6);
            $action[0] = strtolower($action[0]);
        }

        $output = '';
        for($i=0; $i<strlen($action); $i++){
            $ord = ord($action[$i]);
            if($ord >= 65 && $ord <= 90){
                $output .= '_'.strtolower($action[$i]);
            }
            else{
                $output .= $action[$i];
            }
        }
        return $output;
    }
}
