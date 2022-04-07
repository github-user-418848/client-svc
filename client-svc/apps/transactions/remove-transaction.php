<?php
    session_start();
    
    if (isset($_SESSION["queue_generated"])) {
        header("Location: /client-svc/apps/member/services/gen-queue.php");
        die();
    }
    
    if (!isset($_SESSION["has_transaction"])) {
        header("Location: /client-svc/apps/member/services/pin.php");
        die();
    }

    if (!isset($_SESSION["hdid"])) {
        header("Location: /client-svc/apps/index/health-declaration-form.php");
        die();
    }
    
    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/snippets/block-content.php';
    require $_SERVER["DOCUMENT_ROOT"].'/client-svc/utils/connect_db.php';
    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/query.php';
  
    $transactions = "";
    if (security::checkMethod("GET")) {
        
        input::$redirect_location = "transaction-done.php";
        
        $fields = array("id", "queue", "csrf_token");

        input::checkFields($fields, $_GET);

        if (security::checkCSRF($_GET['csrf_token'])) {

            $id = input::checkNum($_GET["id"]);
            $queue = input::checkNum($_GET["queue"]);

            query::pdoInsertQuery("DELETE FROM tbl_transaction WHERE ID=:id AND QUEUE_NUM=:queue_num", array(":id" => $id, "queue_num" => $queue), $con);

            $data = query::pdoSelectQuery("SELECT * FROM tbl_transaction WHERE QUEUE_NUM=:queue_num", array(":queue_num" => $queue), $con);

            $_SESSION["msg"] = '
            <div class="alert alert-info alert-dismissible fade show mx-auto text-center my-4" role="alert" style="max-width: 800px;">
                <i class="me-2 fa fa-info-circle fa-lg" aria-hidden="true"></i>
                <strong>Transaction &#35;'.$id.'</strong> has been removed
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            ';
            
            if (!$data) {
                unset($_SESSION["has_transaction"]);
                header("Location: /client-svc/apps/index/client-type.php");
            }

            header("Location: transaction-done.php");
        }
    }
?>