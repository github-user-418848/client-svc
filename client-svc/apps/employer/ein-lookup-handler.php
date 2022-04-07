<?php

    session_start();
    
    if (isset($_SESSION["queue_generated"])) {
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

        input::$redirect_location = "ein-lookup.php";

        $fields = array("q", "csrf_token", "search");

        input::checkFields($fields, $_POST);

        if (security::checkCSRF($_POST['csrf_token'])) {
            
            $search = input::sanitizeInput($_POST["q"]);

            $query = "SELECT * FROM tbl_employers WHERE EMPID_NO LIKE :id OR EMP_NAME LIKE :name";
            $values_arr = array(":id" => "%".$search."%", ":name" => "%".$search."%");
            $data = query::pdoSelectQuery($query, $values_arr, $con);
            
            if ($data) {
                $_SESSION['id_num'] = $data["EMPID_NO"];
                header("Location: member.php");
            }
            else {
                $_SESSION['msg'] = "
                <div class=\"alert alert-danger alert-dismissible fade show mx-auto text-center my-4\" role=\"alert\" style=\"max-width: 800px;\">
                    <i class=\"fa fa-exclamation-triangle fa-lg me-2\" aria-hidden=\"true\"></i>
                    Sorry, we couldn&#39;t find anything with the information you&#39;ve provided
                    <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>
                </div>";
                header("Location: ein-lookup.php");
            }
        }
    }