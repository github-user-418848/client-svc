<?php
    session_start();

    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/connect_db.php';
    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/security_check.php';
    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/utils/chk_login.php';

    if (!func::checkLoginState($con)) {
        header("Location: login.php");
        die();
    }

    if (security::checkMethod("GET")) {
        
        input::$redirect_location = "client-transaction.php";

        $fields = array("hdid", "queue", "priority_type", "counter_num", "csrf_token");

        input::checkFields($fields, $_GET);

        $hdid = input::checkNum($_GET["hdid"]);
        $queue = input::checkNum($_GET["queue"]);
        $priority_type = input::sanitizeInput($_GET["priority_type"]);
        $counter_num = input::checkNum($_GET["counter_num"]);

        if (security::checkCSRF($_GET['csrf_token'])) {

            $stmt = $con -> prepare('SELECT * FROM tbl_health_declaration WHERE ID=:hdid');
            $stmt -> bindValue(':hdid', $hdid);
            $stmt -> execute();

            if ($stmt -> rowCount() < 1) {
                header("Location: transactions.php");
                die();
            }
                
            $stmt = $con -> prepare("SELECT * FROM tbl_transaction WHERE QUEUE_NUM=:queue_num");
            $stmt -> bindValue(":queue_num", $queue);
            $stmt -> execute();

            if ($stmt -> rowCount() < 1) {
                header("Location: transactions.php");
                die();
            }
            
            $queue_word = "";
            
            if ($queue > 999 && $queue < 10000) {
                $queue_word = $queue;
            }
            if ($queue > 99 && $queue < 1000) {
                $queue_word = "0".$queue;
            }
            if ($queue > 9 && $queue < 100) {
                $queue_word = "00".$queue;
            }
            if ($queue > 0 && $queue < 10) {
                $queue_word = "000".$queue;
            }

            if ($priority_type != "REGULAR") {
                $stmt = $con2 -> prepare("INSERT INTO TBL_BROADCAST (COUNTER_NO, CLIENT_TYPE, QUEUE_NO) VALUES (".$counter_num.", 'PRIORITY', '$queue_word')");
            }
            else {
                $stmt = $con2 -> prepare("INSERT INTO TBL_BROADCAST (COUNTER_NO, CLIENT_TYPE, QUEUE_NO) VALUES (".$counter_num.", '$priority_type', '$queue_word')");
            }
            $stmt -> execute();

            header("Location: client-details.php?hdid=$hdid&queue=$queue");
        }
    }