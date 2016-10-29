<?php
namespace nigiri\plugins;

use nigiri\Controller;

class JsonPlugin implements PluginInterface{

    private $config = [];

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function beforeAction($actionName)
    {
    }

    public function afterAction($actionName, $actionOutput)
    {
        $action = $actionName;
        if(strpos($actionName, 'action')===0){
            $action = substr($actionName, 6);
        }
        $action = Controller::camelCaseToUnderscore($action);

        if(in_array($action, $this->config) or in_array('*',$this->config)){
            header('Content-Type: application/json; charset=utf-8');
            return json_encode($actionOutput);
        }
        else{
            return $actionOutput;
        }
    }
}
