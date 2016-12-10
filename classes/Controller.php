<?php
namespace nigiri;

use nigiri\exceptions\FileNotFound;
use nigiri\plugins\PluginInterface;

/**
 * Interface for all the controllers of the site
 */
abstract class Controller{
    public function __construct(){

    }

    /**
     * Lists all the plugins that this controller uses. Plugins can change the behaviour of the action by executing
     * their code before or after it
     * @return array
     */
    protected function plugins(){
        return [];
    }

    /**
     * Renders a View file
     * @param string $path the path to the view file, without the '.php'. Can be relative to the /views folder or to the root or be absolute
     * @param array $args an array of variables to pass to the view. @see page_include
     * @return string the generated HTML code
     * @throws FileNotFound
     */
    static public function renderView($path, $args= []){
        $p = dirname(__DIR__).'/views/'.$path.'.php';
        if(file_exists($p)){
            return page_include($p, $args);
        }
        elseif(file_exists($path.'.php')){
            return page_include($path.'.php', $args);
        }
        else{
            throw new FileNotFound();
        }
    }

    /**
     * Executes an action of the controller and performs all the necessary operations before and after it
     * @param $action
     * @return string
     */
    public function executeAction($action){
        /** @var PluginInterface[] $plugins */
        $plugins = [];

        //Setup Plugins and execute beforeAction()
        foreach($this->plugins() as $plugin){
            if(!empty($plugin['class']) and class_exists($plugin['class'])){
                $refl = new \ReflectionClass($plugin['class']);
                if($refl->implementsInterface('nigiri\\plugins\\PluginInterface')) {
                    unset($plugin['class']);
                    /** @var PluginInterface $p */
                    $p = $refl->newInstance($plugin);
                    $plugins[] = $p;

                    $p->beforeAction($action);
                }
            }
        }

        //Execute actual Action
        $output = $this->$action();

        //Execute afterAction() on the output
        foreach($plugins as $plugin){
            $output = $plugin->afterAction($action, $output);
        }

        return $output;
    }

    static public function camelCaseToUnderscore($action){
        if(strpos($action, 'action')===0){
            $action = substr($action, 6);
            $action[0] = strtolower($action[0]);
        }

        $output = '';
        for($i=0; $i<strlen($action); $i++){
            $ord = ord($action[$i]);
            if($ord >= 65 && $ord <= 90 && $i!=0){
                $output .= '_'.strtolower($action[$i]);
            }
            else{
                $output .= $action[$i];
            }
        }
        return $output;
    }

    /**
     * Converts a name from underscore_form to CamelCase
     * @param string $str
     * @param bool $first_upper tells whether the first letter should be capitalized or not. Default true
     * @return string
     */
    static public function underscoreToCamelCase($str, $first_upper = true){
        $out = str_replace(' ', '', ucwords(str_replace('_', ' ', $str)));

        if(!$first_upper){
            $out[0] = strtolower($out[0]);
        }

        return $out;
    }
}
