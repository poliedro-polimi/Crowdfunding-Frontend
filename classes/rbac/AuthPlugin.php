<?php
namespace nigiri\rbac;

use nigiri\exceptions\Exception;
use nigiri\models\Permission;
use nigiri\models\Role;
use nigiri\plugins\PluginInterface;
use nigiri\Site;

class AuthPlugin implements PluginInterface{

    const DENY = -1;
    const ANONYMOUS_USER = 1;
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
            if(is_int($config['policy'])){//Anon or Authenticated user
                $this->policy = $config['policy'];
            }
            else{
                try{
                    $this->policy = new Permission($config['policy']);
                }
                catch(Exception $e){//It's not a valid permission name
                    $this->policy = Site::getAuth()->getRole($config['policy']);
                    if(empty($this->policy)){//still empty, so not a valid Role
                        $this->policy = self::DENY;
                    }
                }
            }
        }
        else{
            $this->policy = self::DENY;
        }
    }

    public function beforeAction($actionName)
    {
        if(!empty($this->config['rules'])){

        }
        else{
            //Apply default policy
        }
    }

    public function afterAction($actionName, $actionOutput)
    {
        return $actionOutput;
    }
}
