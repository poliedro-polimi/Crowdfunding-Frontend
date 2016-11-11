<?php
namespace nigiri;

use nigiri\exceptions\FileNotFound;
use nigiri\exceptions\InternalServerError;

/**
 * Finds the pages to execute given the current url
 */
class Router{
    private $page;
    private $controller;
    private $action;

    public function __construct()
    {
        if(!empty($_GET['show_page'])) {
            $this->page = $_GET['show_page'];
        }
        else{
            $this->page = Site::getParam('default_page');
        }

        $boom = explode('/', $this->page);
        if(count($boom)==2){
            $this->controller = Controller::underscoreToCamelCase($boom[0]).'Controller';
            $this->action = Controller::underscoreToCamelCase($boom[1], false);
        }
        elseif(count($boom)==1){
            $this->controller = Controller::underscoreToCamelCase($boom[0]).'Controller';
            $this->action = 'index';
        }
        else{
            throw new InternalServerError("Nessuna home page è stata definita");
        }
    }

    /**
     * Calls the actual controller and method that should handle the request
     * @return string the HTML code to include in the output body
     * @throws FileNotFound
     */
    public function routeRequest(){
        ob_start();//Just a security measure to ensure accidental echos in the controllers don't break the theme output
        if(class_exists('site\\controllers\\'.$this->controller)){
            $class = new \ReflectionClass('site\\controllers\\'.$this->controller);
            if($class->isSubclassOf('nigiri\Controller')) {
                /** @var Controller $instance */
                $instance = $class->newInstance();
                if ($class->hasMethod($this->action)) {
                    return $instance->executeAction($this->action);
                } elseif ($class->hasMethod('action' . ucfirst($this->action))) {
                    return $instance->executeAction('action'.ucfirst($this->action));
                }
            }
        }
        ob_end_clean();
        throw new FileNotFound();
    }

    public function getPage(){
        return $this->page;
    }

    public function getControllerName(){
        return $this->controller;
    }

    public function getActionName(){
        return $this->action;
    }
}
