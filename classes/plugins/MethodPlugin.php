<?php
namespace nigiri\plugins;

use nigiri\Controller;
use nigiri\exceptions\BadRequest;

class MethodPlugin implements PluginInterface{

    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function beforeAction($actionName)
    {
        $action = $actionName;
        if(strpos($actionName, 'action')===0){
            $action = substr($actionName, 6);
        }
        $action = Controller::camelCaseToUnderscore($action);

        if(array_key_exists($action, $this->config)){
            $methods = $this->config[$action];
            if(!is_array($methods)){
                $methods = [$methods];
            }

            $found = false;
            foreach($methods as $m){
                if(strtoupper($m) == strtoupper($_SERVER['REQUEST_METHOD'])){
                    $found = true;
                }
            }

            if(!$found){
                throw new BadRequest("Questa pagina non è accessibile con una richiesta ".strtoupper($_SERVER['REQUEST_METHOD']));
            }
        }
    }

    public function afterAction($actionName, $actionOutput)
    {
        return $actionOutput;
    }
}
