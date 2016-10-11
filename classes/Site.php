<?php
namespace nigiri;

use nigiri\db\DB;
use nigiri\exceptions\Exception;
use nigiri\theme\ThemeInterface;

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
     * @var ThemeInterface
     */
    static private $theme;

    /**
     * @var Router
     */
    static private $router;

    static function init($data){
        if(empty($data['theme']) or !($data['theme'] instanceof ThemeInterface)){
            throw new Exception("Nessun tema configurato per visualizzare il sito", 1, "Non è stato specificato nessun tema o il tema specificato non implementa l'interfaccia ThemeInterface");
        }
        else{
            self::$theme=$data['theme'];
        }

        if(!empty($data['db'])){
            self::initDB($data['db']);
        }

        if(!empty($data['params'])){
            self::$params=$data['params'];
        }

        self::$router = new Router();
    }

    /**
     * @return DB
     */
    public static function DB(){
        return self::$DB;
    }

    public static function getRouter(){
        return self::$router;
    }

    public static function getTheme(){
        return self::$theme;
    }

    /**
     * Switches the current theme with another one.
     * WARNING: strings already passed to the theme with append() will be lost!
     * @param ThemeInterface $t
     */
    public static function switchTheme(ThemeInterface $t){
        self::$theme = $t;
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

    public static function printPage(){
        echo self::$theme->render();
    }

    private static function initDB($db){
        if(!empty($db) and (($db instanceof DB) || is_array($db))){
            if(is_array($db)){
                if(class_exists($db['class'])) {
                    $class = new \ReflectionClass($db['class']);
                    if ($class->isSubclassOf('site\db\DB')){
                        self::$DB = $class->newInstance($db);
                    }
                    else{
                        throw new Exception("Errore nella configurazione del database", 2, "Il database specificato non estende la classe DB");
                    }
                }
                else{
                    throw new Exception("Errore nella configurazione del database", 3, "La classe specificata per il database non esiste");
                }
            }
            else {
                self::$DB = $db;
            }
        }
    }
}
