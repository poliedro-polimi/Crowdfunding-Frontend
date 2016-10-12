<?php
namespace nigiri\models;
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
        if(array_key_exists($name, self::$permissions)){
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
        return self::$permissions[$this->p];
    }

    /**
     * Adds a permission to the list of permissions available in the system
     * NOTE: if you provide a permission name that is already in use, the existing permission will
     * be overwritten
     * @param string $name
     * @param string $description
     */
    static public function addPermission($name, $description){
        self::$permissions[$name] = $description;
    }

    /**
     * Returns the index of the permissions, to sort them in categories
     * @return array
     */
    static public function getIndex(){
        return self::$permissions_index;
    }
}