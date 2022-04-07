<?php
    session_start();
    
    if (isset($_SESSION["queue_generated"])) {
        header("Location: gen-queue.php");
        die();
    }
    
    if (!isset($_SESSION["has_transaction"])) {
        header("Location: /client-svc/apps/index/client-type.php");
        die();
    }

    if (!isset($_SESSION["hdid"])) {
        header("Location: /client-svc/apps/index/health-declaration-form.php");
        die();
    }

    require $_SERVER["DOCUMENT_ROOT"].'/client-svc/utils/connect_db.php';
    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/security_check.php';
    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/query.php';

    if (security::checkMethod("POST")) {
        
        input::$redirect_location = "transaction-done.php";

        $fields = array("transaction", "request", "gov_id", "save");

        input::checkFields($fields, $_POST);

        $choices_transaction = Array("MEMBER DATA RECORD", "PNC/ID CARD", "PREMIUM CONTRIBUTION", "MEMBER UPDATING");
        $choices_gov_id = Array("PHILIPPINE PASSPORT", "PHILHEALTH ID", "DRIVER'S LICENSE", "SSS UMID CARD", "TIN CARD", "POSTAL ID", "VOTER'S ID", "PROFESSIONAL REGULATION COMMISSION ID", "SENIOR CITIZEN ID", "O.F.W ID");

        $transactions = "";
        foreach ($_POST["transaction"] as $value) {
            $transactions .= input::validateChoices($value, $choices_transaction).";";
        }

        if ($_SESSION["member_type"] == "NEW_MEMBER") {
            $request = "FOR REGISTRATION";
        }
        else {
            $choices_request = Array("FOR HOSPITALIZATION", "FOR FILE", "FOR REPLACEMENT");
            $request = input::validateChoices($_POST["request"], $choices_request);
        }
        $id_presented = input::validateChoices($_POST["gov_id"], $choices_gov_id);

        $data = query::pdoInsertQuery("
        UPDATE tbl_transaction 
        SET 
        TRANSACTION_DESC=:transaction, 
        PURPOSE=:request, 
        ID_PRESENTED=:id_presented 
        WHERE ID=:id AND QUEUE_NUM=:queue_num", 
        array(
            ":transaction" => $transactions, 
            ":request" => $request, 
            ":id_presented" => $id_presented, 
            ":id" => $_SESSION["edit_id"], 
            "queue_num" => $_SESSION["edit_queue"]
        ), $con);
        
        $_SESSION["msg"] = '
        <div class="alert alert-info alert-dismissible fade show mx-auto text-center my-4" role="alert" style="max-width: 800px;">
            <i class="me-2 fa fa-info-circle fa-lg" aria-hidden="true"></i>
            <strong>Transaction &#35;'.$_SESSION["edit_id"].'</strong> has been updated
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        ';

        header("Location: transaction-done.php");
    }