<?php
namespace nigiri\rbac;
use nigiri\exceptions\Exception;

/**
 * Models for the Authorization Permissions
 * They are not stored on the DB since they are closely tied to the code.
 * We store them here, like some kind of enumeration
 */
class Permission{
    /** @var array the list of permissions in the site */
    static private $permissions = [];

    /** @var array sorts the permissions in categories for the RBAC management GUI */
    static private $permissions_index = [];

    private $p;

    public function __construct($name)
    {
        if(array_key_exists($name, self::getPermissions())){
            $this->p = $name;
        }
        else{
            throw new Exception("Il permesso specificato non esiste");
        }
    }

    public function getName(){
        return $this->p;
    }

    public function getDescription(){
        $p = self::getPermissions();
        return $p[$this->p];
    }

    /**
     * Adds a permission to the list of permissions available in the system, temporarily
     * NOTE: if you provide a permission name that is already in use, the existing permission will
     * be overwritten
     * @param string $name
     * @param string $description
     */
    static public function addPermission($name, $description){
        $p = self::getPermissions();
        $p[$name] = $description;
    }

    static public function getPermissions(){
        if(empty(self::$permissions)){
            self::loadFile(self::$permissions, dirname(dirname(__DIR__)).'/includes/permissions.php');
        }
        return self::$permissions;
    }

    /**
     * Returns the index of the permissions, to sort them in categories
     * @return array
     */
    static public function getIndex(){
        if(empty(self::$permissions_index)){
            self::loadFile(self::$permissions_index, dirname(dirname(__DIR__)).'/includes/permissions_index.php');
        }
        return self::$permissions_index;
    }

    static private function loadFile(&$var, $file){
        if(file_exists($file)){
            $var = require $file;
        }
    }
}
