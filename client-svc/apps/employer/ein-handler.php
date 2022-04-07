<?php

    session_start();
    
    if (isset($_SESSION["queue_generated"])) {
        $_SESSION['msg'] = "
        <div class=\"alert alert-info alert-dismissible fade show mx-auto text-center my-4\" role=\"alert\" style=\"max-width: 800px;\">
            <i class=\"fa fa-exclamation-triangle fa-lg me-2\" aria-hidden=\"true\"></i>
            You can't add another transaction once you have your queue number. Inquire at the frontline area if you have any additional transactions.
            <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>
        </div>";
        header("Location: /client-svc/apps/transactions/gen-queue.php");
        die();
    }

    if (!isset($_SESSION["hdid"])) {
        header("Location: /client-svc/apps/index/health-declaration-form.php");
        die();
    }

    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/connect_db.php';
    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/security_check.php';
    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/query.php';

    
    if (security::checkMethod("POST")) {

        input::$redirect_location = "ein.php";

        $fields = array("ein", "csrf_token", "submit");

        input::checkFields($fields, $_POST);

        if (security::checkCSRF($_POST['csrf_token'])) {
    
            $id = input::checkNum($_POST["ein"]);
            
            $query = "SELECT * FROM tbl_employers WHERE EMPID_NO=:id LIMIT 1";
            $values_arr = array(":id" => $id);
            $data = query::pdoSelectQuery($query, $values_arr, $con);

            if ($data) {
                $_SESSION['id_num'] = $data["EMPID_NO"];
                header("Location: member.php");
            }
            else {
                $_SESSION['msg'] = "
                <div class=\"alert alert-danger alert-dismissible fade show mx-auto text-center my-4\" role=\"alert\" style=\"max-width: 800px;\">
                    <i class=\"fa fa-exclamation-triangle fa-lg me-2\" aria-hidden=\"true\"></i>
                        ID num <strong>$id</strong> doesn&#39;t exist on the records
                    <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>
                </div>";
                header("Location: ein.php");
            }
        }
    }