<?php
namespace nigiri\rbac;

/**
 * Gives the requirements needed for an object to be used as the representation of the authenticated user in the Auth component
 */
interface AuthUserInterface{
    public function getId();

    public static function getLoggedInUser();
}
