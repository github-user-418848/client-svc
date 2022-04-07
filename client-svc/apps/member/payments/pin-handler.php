<?php

    session_start();
    
    if (!isset($_SESSION["hdid"])) {
        header("Location: /client-svc/apps/index/health-declaration-form.php");
        die();
    }

    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/connect_db.php';
    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/security_check.php';
    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/query.php';

    if (security::checkMethod("POST")) {

        input::$redirect_location = "pin.php";
        
        $fields = array("pin", "csrf_token", "submit");

        input::checkFields($fields, $_POST);

        if (security::checkCSRF($_POST['csrf_token'])) {

            $id = input::checkNum($_POST["pin"]);
            
            $query = "SELECT * FROM tbl_members WHERE MEMID_NO=:id";
            $values_arr = array(":id" => $id);
            $data = query::pdoSelectQuery($query, $values_arr, $con);

            if ($data) {
                $_SESSION['id_num'] = $data["MEMID_NO"];
                header("Location: contributions.php");
            }
            else {
                $_SESSION['msg'] = "
                <div class=\"alert alert-danger alert-dismissible fade show mx-auto text-center my-4\" role=\"alert\" style=\"max-width: 800px;\">
                    <i class=\"fa fa-exclamation-triangle fa-lg me-2\" aria-hidden=\"true\"></i>
                        ID num <strong>$id</strong> doesn&#39;t exist on the records
                    <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>
                </div>";
                header("Location: pin.php");
            }
        }
    }