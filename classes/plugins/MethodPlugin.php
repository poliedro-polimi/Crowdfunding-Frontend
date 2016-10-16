<?php
namespace nigiri\plugins;

use nigiri\exceptions\BadRequest;

class MethodPlugin implements PluginInterface{

    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function beforeAction($actionName)
    {
        if(array_key_exists($actionName, $this->config)){
            $methods = $this->config[$actionName];
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