<?php
session_start();
require_once(__DIR__ . "/db.php");

function is_logged_in(){
    return isset($_SESSION["user"]);
}
function has_role($role){
    if(is_logged_in() && isset($_SESSION["user"]["roles"])){
        foreach($_SESSION["user"]["roles"] as $r){
            if($r["name"] == $role){
                return true;
            }
        }
    }
    return false;
}
?>