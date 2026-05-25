<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


function require_role($required_roles) {
    if(!isset($_SESSION['user_id'])) {
        header('Location /login.php');
        exit();

    }

    if(!is_array($required_roles)) {
        $required_roles = [$required_roles];
    }

    if(!in_array($_SESSION['role'], $required_roles)) {

        header('Location: /index.php');
        exit();
    }



}
?>