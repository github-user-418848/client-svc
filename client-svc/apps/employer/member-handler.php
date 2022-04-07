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

    if (!isset($_SESSION['id_num'])) {
        header("Location: ein.php");
        die();
    }

    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/connect_db.php';
    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/security_check.php';
    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/query.php';

    if (security::checkMethod("POST")) {
        
        input::$redirect_location = "member.php";

        $fields = array("transaction", "request", "img-data", "csrf_token", "submit");

        input::checkFields($fields, $_POST);

        if (security::checkCSRF($_POST['csrf_token'])) {

            $choices_transaction = Array("MEMBER DATA RECORD", "PNC/ID CARD", "MEMBERS UPDATING", "RECORD UPDATING", "REQUEST CERTIFICATE FOR BUSINESS PERMIT");
            $choices_request = Array("FOR HOSPITALIZATION", "FOR FILE", "FOR REPLACEMENT");
            
            $transactions = "";
            foreach ($_POST["transaction"] as $value) {
                $transactions .= input::validateChoices($value, $choices_transaction).";";
            }

            $transactions_arr = explode(";", $transactions);

            $member_update_count = "";

            if (in_array("MEMBERS UPDATING", $transactions_arr)) {
                if (!isset($_POST["emp_num"])) {
                    header("Location: member.php");
                    die();
                }
                $member_update_count = input::checkNum($_POST["emp_num"]);
            }

            $request = input::validateChoices($_POST["request"], $choices_request);
            $signature = $_POST["img-data"];
            
            $query = "SELECT * FROM tbl_employers WHERE EMPID_NO=:id LIMIT 1";
            $values_arr = array(":id" => $_SESSION["id_num"]);
            $data = query::pdoSelectQuery($query, $values_arr, $con);
        
            if ($data && !empty($signature)) {

                if (!isset($_SESSION["queue"])) {
                    // Development Only

                    // $_SESSION["queue"] = rand(1000, 9999);

                    // When used in production

                    $stmt = $con2 -> prepare("SELECT * FROM TBL_QUEUING_NUMBERS WHERE QUEUE_DATE='".date("Y-m-d")."'");
                    $stmt -> execute();
                    $data2 = $stmt -> fetch();

                    if ($_SESSION["priority"] == "REGULAR") {
                        // regular client

                        if (!empty($data2["QUEUE_DATE"])) {
                            $queue = $data2["SERVICE_REGULAR"] + 1;
                            $stmt = $con2 -> prepare("UPDATE TBL_QUEUING_NUMBERS SET SERVICE_REGULAR='$queue' WHERE QUEUE_DATE='".date("Y-m-d")."'");
                            $stmt -> execute();
                        }
                        else {
                            $stmt = $con2 -> prepare("INSERT INTO TBL_QUEUING_NUMBERS (QUEUE_DATE, SERVICE_REGULAR) VALUES ('".date("Y-m-d")."', '1')");
                            $stmt -> execute();
                        }

                        $_SESSION["queue"] = $queue;
                    } 
                    else {
                        // priority client

                        if (!empty($data2["QUEUE_DATE"])) {
                            $queue = $data2["SERVICE_PRIORITY"] + 1;
                            $stmt = $con2 -> prepare("UPDATE TBL_QUEUING_NUMBERS SET SERVICE_PRIORITY='$queue' WHERE QUEUE_DATE='".date("Y-m-d")."'");
                            $stmt -> execute();
                        }
                        else {
                            $stmt = $con2 -> prepare("INSERT INTO TBL_QUEUING_NUMBERS (QUEUE_DATE, SERVICE_PRIORITY) VALUES ('".date("Y-m-d")."', '1')");
                            $stmt -> execute();
                        }

                        $_SESSION["queue"] = $queue;
                    }

                }
                $encoded_image = explode(",", $signature)[1];
                $decoded_image = base64_decode($encoded_image);
                $filename = bin2hex(openssl_random_pseudo_bytes(16));
                $filepath =  "../../signatures/".$filename.".png";
                file_put_contents($filepath, $decoded_image);

                $query = "INSERT INTO tbl_transaction (TRANSACTION_ID, HDID, PIN_PEN, TRANSACTION_DATE, TRANSACTION_DESC, PURPOSE, NAME, ADDRESS, REQUESTOR_NAME, PRIORITY, QUEUE_NUM, MEMBER_UPDATE_COUNT, CLIENT_TYPE, ESIGNATURE)
                VALUES (:id, :hdid, :pin, :transaction_date, :transaction, :request, :name, :addr, :requestor_name, :priority, :queue_num, :member_update_count, :client_type,  :signature)";

                $values_arr = array(
                    ":id" => $_SESSION["id_num"].date("mdY"), ":pin" => $_SESSION["id_num"], ":hdid" => $_SESSION["hdid"], 
                    ":requestor_name" => $_SESSION["requestor_name"], ":priority" => $_SESSION["priority"],":name" => $data["EMP_NAME"], 
                    ":addr" => $data["ADDRESS"], ":transaction_date" => date("Y-m-d"), ":transaction" => $transactions, ":request" => $request, ":queue_num" => $_SESSION["queue"], 
                    ":member_update_count" => $member_update_count, ":client_type" => "EMPLOYER", ":signature" => $filepath
                );

                query::pdoInsertQuery($query, $values_arr, $con);

                $_SESSION["has_transaction"] = "1";
                $_SESSION["feedback"] = "1";
                $_SESSION["msg"] = '
                <div class="text-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="95" height="95" fill="currentColor" class="bi bi-check-circle text-success mb-3" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                </svg>
                <h5>Transaction added successfully!</h5>
                <p class="text-muted small">You may now generate your queue number, or add another transaction.</p>
                </div>';

                unset($_SESSION['id_num']);
                header("Location: /client-svc/apps/transactions/transaction-done.php");
            }
        }
    }