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
    private $method;

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
            $this->method = Controller::underscoreToCamelCase($boom[1], false);
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
                if ($class->hasMethod($this->method)) {
                    return $instance->executeAction($this->method);
                } elseif ($class->hasMethod('action' . ucfirst($this->method))) {
                    return $instance->executeAction('action'.ucfirst($this->method));
                }
            }
        }
        ob_end_clean();
        throw new FileNotFound();
    }

    public function getPage(){
        return $this->page;
    }

    public function getController(){
        return $this->controller;
    }

    public function getMethod(){
        return $this->method;
    }
}
