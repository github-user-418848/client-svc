<?php

    session_start();
    
    if (isset($_SESSION["queue_generated"])) {
        header("Location: /client-svc/apps/member/services/gen-queue.php");
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

        input::$redirect_location = "pin-lookup.php";

        $fields = array("last_name", "first_name", "mid_name", "birthdate", "csrf_token", "submit");

        input::checkFields($fields, $_POST);

        if (security::checkCSRF($_POST['csrf_token'])) {
        
            $last_name = input::sanitizeInput($_POST["last_name"]);
            $first_name = input::sanitizeInput($_POST["first_name"]);
            $middle_name = input::sanitizeInput($_POST["mid_name"]);
            $birthdate = input::sanitizeInput($_POST["birthdate"]);

            $query = "SELECT * FROM tbl_members WHERE FIRSTNAME=:first_name AND MIDDLENAME=:middle_name AND LASTNAME=:last_name AND BIRTHDAY=:birthdate";
            $values_arr = array(":last_name" => $last_name, ":first_name" => $first_name, ":middle_name" => $middle_name, ":birthdate" => $birthdate);
            $data = query::pdoSelectQuery($query, $values_arr, $con);
            
            if ($data) {
                $_SESSION['id_num'] = $data["MEMID_NO"];
                header("Location: member.php");
            }
            else {
                $_SESSION['msg'] = "
                <div class=\"alert alert-danger alert-dismissible fade show mx-auto text-center my-4\" role=\"alert\" style=\"max-width: 800px;\">
                    <i class=\"fa fa-exclamation-triangle fa-lg me-2\" aria-hidden=\"true\"></i>
                    Sorry, we couldn&#39;t find anything with the information you&#39;ve provided
                    <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>
                </div>";
                header("Location: pin-lookup.php");
            }
        }
    }