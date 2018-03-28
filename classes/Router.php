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
    private $language;

    public function __construct()
    {
        if(!empty($_GET['show_page'])) {
            $this->page = $_GET['show_page'];
        }
        else{
            $this->page = Site::getParam('default_page');
        }

        $boom = array_filter(explode('/', $this->page));

        $lang = Site::getParam("languages", []);
        if(in_array($boom[0], $lang)){
            $this->language = array_shift($boom);
        }
        else{
            $this->language = Site::getParam('default_language');
        }

        if(count($boom)==1){
            $boom[1] = 'index';
        }

        if(count($boom)==2){
            $this->controller = Controller::underscoreToCamelCase($boom[0]).'Controller';
            $this->action = Controller::underscoreToCamelCase(empty($boom[1])?'index':$boom[1], false);
        }
        else{
            if(empty($this->page)) {
                throw new InternalServerError("Nessuna home page è stata definita");
            }
            else{
                new FileNotFound("", 'Impossibile trovare '.$this->page);
            }
        }
    }

    /**
     * Calls the actual controller and method that should handle the request
     * @return string the HTML code to include in the output body
     * @throws FileNotFound
     */
    public function routeRequest()
    {
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

    public function getPage()
    {
        return $this->page;
    }

    public function getControllerName()
    {
        return $this->controller;
    }

    public function getActionName()
    {
        return $this->action;
    }

    public function getRequestedLanguage()
    {
        return $this->language;
    }

    /**
     * Checks if a url points to the current page, given routing rules
     * @param string $page
     * @return bool
     */
    public function isCurrentPage($page)
    {
        if($page==$this->page){
            return true;
        }

        $boom = explode('/', $page);
        $lang = Site::getParam("languages", []);
        if(in_array($boom[0], $lang)){
            array_shift($boom);
        }

        if(count($boom)==2){
            return $boom[0]==Controller::camelCaseToUnderscore(substr($this->controller, 0, -10)) && $boom[1]==Controller::camelCaseToUnderscore($this->action);
        }
        elseif(count($boom)==1){
            return $boom[0]==Controller::camelCaseToUnderscore(substr($this->controller, 0, -10)) && Controller::camelCaseToUnderscore($this->action)=='index';
        }
    }
}
