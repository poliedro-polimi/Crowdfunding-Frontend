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
            $this->controller = $this->underscoreToCamelCase($boom[0]).'Controller';
            $this->method = $this->underscoreToCamelCase($boom[1], false);
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
        if(class_exists('site\\controllers\\'.$this->controller)){
            $class = new \ReflectionClass($this->controller);
            if($class->isSubclassOf('site\Controller')) {
                if ($class->hasMethod($this->method)) {
                    $instance = $class->newInstance();
                    $meth = $this->method;
                    return $instance->$meth();
                } elseif ($class->hasMethod('action' . ucfirst($this->method))) {
                    $instance = $class->newInstance();
                    $meth = 'action'.ucfirst($this->method);
                    return $instance->$meth();
                }
            }
        }
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

    /**
     * Converts a name from underscore_form to CamelCase
     * @param string $str
     * @param bool $first_upper tells whether the first letter should be capitalized or not. Default true
     * @return string
     */
    private function underscoreToCamelCase($str, $first_upper = true){
        $out = str_replace(' ', '', ucwords(str_replace('_', ' ', $str)));

        if(!$first_upper){
            $out[0] = strtolower($out[0]);
        }

        return $out;
    }
}
