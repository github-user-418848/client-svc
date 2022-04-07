<?php

    session_start();

    if (isset($_SESSION["queue_generated_emp"])) {
        header("Location: /client-svc/apps/employer/gen-queue.php");
        die();
    }

    if (isset($_SESSION["queue_generated"])) {
        header("Location: /client-svc/apps/member/services/gen-queue.php");
        die();
    }

    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/connect_db.php';
    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/security_check.php';
    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/query.php';

    if (security::checkMethod("POST")) {

        input::$redirect_location = "health-declaration-form.php";

        $fields = array(
            "last_name", "first_name", "mid_name", "addr", "contact", "temperature", "symptom_fever", "symptom_cough", 
            "symptom_colds", "symptom_sore", "symptom_short", "symptom_diarrhea", "frontliner", "contact2", "priority_type",
            "csrf_token", "submit"
        );

        input::checkFields($fields, $_POST);

        if (security::checkCSRF($_POST['csrf_token'])) {

            $choices_yes_no = Array("YES", "NO");
            $choices_priority = Array("REGULAR", "SENIOR", "PREGNANT/NURSING", "P.W.D");
            $choices_client_type = Array("MEMBER", "EMPLOYER");

            $last_name = input::sanitizeInput($_POST["last_name"]);
            $first_name = input::sanitizeInput($_POST["first_name"]);
            $middle_name = input::sanitizeInput($_POST["mid_name"]);
            $addr = input::sanitizeInput($_POST["addr"]);
            $contact1 = input::checkNum($_POST["contact"]);
            $temperature = input::checkDecimal($_POST["temperature"]);
            $fever = input::validateChoices($_POST["symptom_fever"], $choices_yes_no);
            $cough = input::validateChoices($_POST["symptom_cough"], $choices_yes_no);
            $colds = input::validateChoices($_POST["symptom_colds"], $choices_yes_no);
            $sore = input::validateChoices($_POST["symptom_sore"], $choices_yes_no);
            $short = input::validateChoices($_POST["symptom_short"], $choices_yes_no);
            $diarrhea = input::validateChoices($_POST["symptom_diarrhea"], $choices_yes_no);
            $contact2 = input::validateChoices($_POST["contact2"], $choices_yes_no);
            $frontliner = input::validateChoices($_POST["frontliner"], $choices_yes_no);
            $priority = input::validateChoices($_POST["priority_type"], $choices_priority);
            
            $letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
            $token = substr(str_shuffle($letters), 0, 64);

            $query = "INSERT INTO tbl_health_declaration (TOKEN, LASTNAME, FIRSTNAME, MIDDLENAME, TEMPERATURE, ADDRESS, CONTACT_NUMBER, FEVER, COUGH, COLDS, SORE_THROAT, SHORTNESS_OF_BREATH, DIARRHEA, FRONTLINER, CONTACT_TRACE)
            VALUES (:token, :last_name, :first_name, :middle_name, :temperature, :addr, :contact1, :fever, :cough, :colds, :sore, :short, :diarrhea, :frontliner, :contact2)";

            $values_arr = array("token" => $token, ":last_name" => $last_name, ":first_name" => $first_name, ":middle_name" => $middle_name, ":temperature" => $temperature, ":addr" => $addr,
            ":contact1" => $contact1, ":fever" => $fever, ":cough" => $cough, ":colds" => $colds, ":sore" => $sore, ":short" => $short, ":diarrhea"=> $diarrhea,
            ":frontliner" => $frontliner, ":contact2" => $contact2);
            
            query::pdoInsertQuery($query, $values_arr, $con);
            setcookie('hdf_dat', $token,  time()+86400, "/", null, false, true);
            setcookie('p_type', $priority,  time()+86400, "/", null, false, true);

            session_unset();

            if ($temperature > 37 || $fever == "YES" || $cough == "YES" || $colds == "YES" || $sore == "YES" || $short == "YES" || $diarrhea == "YES" || $contact2 == "YES") {
                $_SESSION['notify'] = "1";
            }

            $_SESSION["hdid"] = $con -> lastInsertId();
            $_SESSION["requestor_name"] = $last_name.', '.$first_name.' '.$middle_name;
            $_SESSION["priority"] = $priority;

            header("Location: client-type.php");
        }

    }