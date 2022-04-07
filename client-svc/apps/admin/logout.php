<?php session_start();
    
    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/utils/chk_login.php';
    
    func::deleteSession();
    func::deleteCookie();

    header("Location: login.php");