<?php
    session_start();
    
    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/security_check.php';
    
    if (isset($_SESSION["queue_generated"])) {
        header("Location: /client-svc/apps/transactions/gen-queue.php");
        die();
    }
    
    if (!isset($_SESSION["hdid"])) {
        header("Location: /client-svc/apps/index/health-declaration-form.php");
        die();
    }
    
    if (security::checkMethod("GET")) {
        
        input::$redirect_location = "ein-lookup.php";

        $fields = array("ein", "csrf_token");

        input::checkFields($fields, $_GET);

        if (security::checkCSRF($_GET['csrf_token'])) {

            $ein = input::checkNum($_GET["ein"]);
            
            $_SESSION["id_num"] = $ein;
            header("Location: member.php");
        }
    }