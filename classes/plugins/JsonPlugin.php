<?php
namespace nigiri\plugins;

class JsonPlugin implements PluginInterface{

    public function __construct($config)
    {

    }

    public function beforeAction($actionName)
    {
    }

    public function afterAction($actionName, $actionOutput)
    {
        header('Content-Type: application/json; charset=utf-8');
        return json_encode($actionOutput);
    }
}