<?php
namespace nigiri\plugins;

interface PluginInterface{
    public function __construct($config);

    public function beforeAction($actionName);

    public function afterAction($actionName, $actionOutput);
}
