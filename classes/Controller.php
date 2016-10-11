<?php
namespace site;

use site\exceptions\FileNotFound;

/**
 * Interface for all the controllers of the site
 */
abstract class Controller{
    abstract public function __construct();

    /**
     * Renders a View file
     * @param string $path the path to the view file, without the '.php'. Can be relative to the /views folder or to the root or be absolute
     * @param array $args an array of variables to pass to the view. @see page_include
     * @return string the generated HTML code
     * @throws FileNotFound
     */
    protected function renderView($path, $args){
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
}
