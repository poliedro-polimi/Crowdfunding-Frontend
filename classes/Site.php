<?php
namespace site;

use site\db\DB;
use site\theme\ThemeInterface;

/**
 * The main class of the framework. Represents the website and its resources and data
 */
class Site{
    /**
     * @var DB
     */
    static private $DB=null;
    static private $params=[];

    /**
     * @var site/theme/ThemeInterface
     */
    static private $theme;

    static function init($data){
        if(empty($data['theme']) or !($data['theme'] instanceof ThemeInterface)){
            //TODO print fatal error page
        }
        else{
            self::$theme=$data['theme'];
        }

        if(!empty($data['db']) and (($data['db'] instanceof DB) || is_array($data['db']))){
            if(is_array($data['db'])){
                if(class_exists($data['db']['class'])) {
                    $class = new \ReflectionClass($data['db']['class']);
                    if ($class->isSubclassOf('site\db\DB')){
                        self::$DB = $class->newInstance($data['db']);
                    }
                    else{
                        //TODO print fatal error
                    }
                }
                else{
                    //TODO print fatal error
                }
            }
            else {
                self::$DB = $data['db'];
            }
        }

        if(!empty($data['params'])){
            self::$params=$data['params'];
        }
    }

    /**
     * @return DB
     */
    static function DB(){
        return self::$DB;
    }

    /**
     * Finds a static site parameter
     * @param $name
     * @return mixed|null
     */
    public static function getParam($name, $default=null){
        if(key_exists($name, self::$params)){
            return self::$params[$name];
        }
        return $default;
    }

    //TODO Routing?

    //TODO Url generation?
}
