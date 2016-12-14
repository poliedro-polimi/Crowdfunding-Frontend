<?php
namespace nigiri\rbac;

use nigiri\db\DBException;
use nigiri\exceptions\Exception;
use nigiri\exceptions\InternalServerError;
use nigiri\Site;

/**
 * The Authorization/Authentication Component
 * It implements a RBAC Authorization structure
 */
class Auth{

    /** @var AuthUserInterface the currently logged in user */
    private $user = null;
    private $userClass = null;

    public function __construct($userClass){
        try {
            $c = new \ReflectionClass($userClass);
            if ($c->implementsInterface('nigiri\\rbac\\AuthUserInterface')) {
                $this->userClass = $c;
            } else {
                throw new InternalServerError("Configurazione errata", "La classe utente specificata per l'autorizzazione non è valida");
            }
        }
        catch (\ReflectionException $e){
            throw new InternalServerError("Configurazione errata", "La classe utente specificata per l'autorizzazione non esiste");
        }
    }

    /**
     * Assigns a Permission to a Role
     * @param string|Permission $p the permission to assign
     * @param string|Role $r the role name
     * @throws DBException
     */
    public function assignPerm($p,$r){
        if(!($p instanceof Permission)) {
            new Permission($p);//Check if permission exists. If it doesn't exception is thrown
        }
        if($r instanceof Role){
           $r = $r->getName();
        }
        try {
            Site::DB()->query("INSERT INTO roles_permissions (role, permission) VALUE ('".
                Site::DB()->escape($r)."', '".Site::DB()->escape($p)."')");
        }
        catch(DBException $e){
            if($e->getCode()!=1062){//Ignore Duplicate entries errors
                throw $e;
            }
        }
    }

    /**
     * Deletes the assignment of a permission to a role
     * @param string|Permission $p
     * @param string|Role $r
     */
    public function deletePerm($p, $r){
        if($r instanceof Role){
            $r = $r->getName();
        }
        if($p instanceof Permission){
            $p = $p->getName();
        }

        Site::DB()->query("DELETE FROM roles_permissions WHERE role='".
            Site::DB()->escape($r)."' AND permission='".Site::DB()->escape($p)."'");
    }

    public function getRole($r){
        return Role::findOne(['name' => $r]);
    }

    public function addRole($r, $display=''){
        Site::DB()->query("INSERT INTO role (`name`, display) VALUE ('".
            Site::DB()->escape($r)."', '".Site::DB()->escape($display)."')");
    }

    /**
     * @param string|Role $r
     * @throws InternalServerError
     */
    public function deleteRole($r){
        if($r instanceof Role){
            $r = $r->getName();
        }

        try {
            Site::DB()->startTransaction();
            Site::DB()->query("DELETE FROM roles_permissions WHERE role='".
                Site::DB()->escape($r)."'");
            Site::DB()->query("DELETE FROM role WHERE `name`='".
                Site::DB()->escape($r)."'");
            Site::DB()->query("DELETE FROM users_roles WHERE role='".
                Site::DB()->escape($r)."'");

            Site::DB()->commitTransaction();
        }
        catch (DBException $e){
            Site::DB()->rollbackTransaction();
            $e->logError("Cancellazione ruolo");
            throw new InternalServerError("Si è verificato un errore nella cancellazione del ruolo");
        }
    }

    /**
     * Checks if a role or a group of roles have a specific permission
     * @param Role[]|string[]|string|Role $r
     * @param string|Permission $perm
     * @return bool
     */
    public function roleCan($r, $perm){
        if(!is_array($r)){
            $r = [$r];
        }

        foreach($r as $k=>$v){
            if(is_string($v)) {
                $r[$k] = "'" . Site::DB()->escape($v) . "'";
            }
            elseif($v instanceof Role){
                $r[$k] = "'" . Site::DB()->escape($v->getName()) . "'";
            }
            else{
                unset($r[$k]);
            }
        }

        if($perm instanceof Permission){
            $perm = $perm->getName();
        }

        $q = "SELECT COUNT(*) AS N FROM roles_permissions WHERE role IN (".implode(', ', $r).") AND permission='".
            Site::DB()->escape($perm)."'";

        $res = Site::DB()->query($q, true);
        return $res['N'] > 0;
    }

    /**
     * Checks if a user has a specific permission
     * @param string|AuthUserInterface $uid
     * @param string|Permission $perm
     * @return bool
     */
    public function userCan($uid, $perm){
        $roles = $this->getUserRoles($uid);
        return $this->roleCan($roles, $perm);
    }

    /**
     * Checks if the current user has a specific permission, it works even with permissions of anonymous users
     * @param string|Permission $perm
     * @return bool
     */
    public function iCan($perm){
        return $this->userCan($this->getLoggedInUser(), $perm);
    }

    /**
     * Finds the roles a user is assigned to
     * @param string|AuthUserInterface $uid
     * @return Role[]
     */
    public function getUserRoles($uid){
        if(is_object($uid) && $uid instanceof AuthUserInterface){
            $uid = $uid->getId();
        }

        return array_merge(Role::find([
            'search_joins' => 'users',
            'search_literal' => 1,
            "users.uid = '".Site::DB()->escape($uid)."'"
        ]), $this->isLoggedIn()?[Role::getAuthenticatedUserRole()]:[Role::getAnonymousUserRole()]);
    }

    /**
     * @param string|AuthUserInterface $uid
     * @param string|Role $r
     * @return bool
     */
    public function userHasRole($uid, $r){
        if($r instanceof Role){
            $r = $r->getName();
        }

        if(is_object($uid) && $uid instanceof AuthUserInterface){
            $uid = $uid->getId();
        }

        if($r==Role::AUTHENTICATED_USER && $this->isLoggedIn()){
            return true;
        }
        elseif($r==Role::ANONYMOUS_USER && !$this->isLoggedIn()){
            return true;
        }

        $result = Site::DB()->query("SELECT COUNT(*) AS N FROM users_roles WHERE `user`='".Site::DB()->escape($uid).
          "' AND role='".Site::DB()->escape($r)."'", true);

        return $result['N']>0;
    }

    /**
     * @param string|AuthUserInterface $uid
     * @param Role|string $r
     * @throws DBException
     */
    public function addUserRole($uid, $r){
        if($r instanceof Role){
            $r = $r->getName();
        }

        if(is_object($uid) && $uid instanceof AuthUserInterface){
            $uid = $uid->getId();
        }

        try {
            Site::DB()->query("INSERT INTO users_roles (`user`, role) VALUES ('" . Site::DB()->escape($uid) .
                "','" . Site::DB()->escape($r) . "')");
        }
        catch(DBException $e){
            if($e->getCode()!=1062){//Ignore Duplicate entries errors
                throw $e;
            }
        }
    }

    /**
     * @param string|AuthUserInterface $uid
     * @param Role $r
     */
    public function deleteUserRole($uid, $r){
        if($r instanceof Role){
            $r = $r->getName();
        }

        if(is_object($uid) && $uid instanceof AuthUserInterface){
            $uid = $uid->getId();
        }

        Site::DB()->query("DELETE FROM users_roles WHERE `user`='".
            Site::DB()->escape($uid)."' AND role='".Site::DB()->escape($r)."'");
    }

    /**
     * @param AuthUserInterface $user
     */
    public function login($user){
        $this->user=$user;
        $_SESSION['uid'] = $user->getId();
    }

    public function logout(){
        $this->user = null;
        unset($_SESSION['uid']);
    }

    public function isLoggedIn(){
        if($this->user===null){
            $this->getLoggedInUser();
        }

        return $this->user!==null;
    }

    /**
     * @return AuthUserInterface|null
     */
    public function getLoggedInUser(){
        if($this->user===null){
            $this->user = $this->userClass->getMethod('getLoggedInUser')->invoke(null);
        }
        return $this->user;
    }
}
