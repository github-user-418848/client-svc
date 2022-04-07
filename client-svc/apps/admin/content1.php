<?php
    
    session_start();

    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/connect_db.php';
    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/security_check.php';
    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/utils/chk_login.php';

    if (!func::checkLoginState($con)) {
        header("Location: login.php");
        die();
    }

    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/query.php';

    function num_of_transactions($transactions) {
        $temp = "";
        if ($transactions > 1) {
            $temp = "
            <span class=\"badge rounded-pill bg-success\">
            MULTIPLE TRANSACTION
            </span>";
        }
        else {
            $temp = "
            <span class=\"badge rounded-pill bg-success\">
            SINGLE TRANSACTION
            </span>";
        }
        return $temp;
    }

    function priority_type($priority_type) {
        $temp = "";
        if ($priority_type != "REGULAR") {
            $temp = "
            <span class=\"badge rounded-pill bg-danger\">
                PRIORITY
            </span>";
        }
        return $temp;
    }

    function client_type($client_type) {
        $temp = "
        <span class=\"badge rounded-pill bg-primary\">
            $client_type
        </span>";
        return $temp;
    }

    if (isset($_GET["status"])) {
        input::$redirect_location = "transactions.php";

        $status = input::validateChoices($_GET["status"], array("PENDING", "ON PROCESS", "DONE", "CANCELLED"));
        echo "<h2>$status</h2><hr>";
        $stmt = $con -> prepare("
            SELECT 
            TBL.HDID, TBL.QUEUE_NUM, TBL.REQUESTOR_NAME, TBL.STATUS, TBL.PRIORITY, TBL.TRANSACTION_NUM,
            CASE WHEN (TBL_EMP.EMP IS NULL) OR (TBL_MEM.MEM IS NULL) THEN TBL.CLIENT_TYPE ELSE 'MEMBER AND EMPLOYER' END CLIENT_TYPE

            FROM (
                SELECT HDID, CLIENT_TYPE,
                QUEUE_NUM, REQUESTOR_NAME, STATUS, 
                PRIORITY, COUNT(ID) TRANSACTION_NUM
                FROM tbl_transaction 
                WHERE STATUS=:status1 AND DATE(TRANSACTION_DATE)=CURRENT_DATE
                GROUP BY QUEUE_NUM
            ) TBL

            LEFT JOIN (
                SELECT QUEUE_NUM, CLIENT_TYPE EMP, STATUS 
                FROM tbl_transaction 
                WHERE CLIENT_TYPE='EMPLOYER' AND STATUS=:status2
                AND DATE(TRANSACTION_DATE)=CURRENT_DATE
                GROUP BY QUEUE_NUM
            ) TBL_EMP

            ON TBL.QUEUE_NUM=TBL_EMP.QUEUE_NUM

            LEFT JOIN (
                SELECT QUEUE_NUM, CLIENT_TYPE MEM, STATUS
                FROM tbl_transaction 
                WHERE CLIENT_TYPE='MEMBER' AND STATUS=:status3
                AND DATE(TRANSACTION_DATE)=CURRENT_DATE
                GROUP BY QUEUE_NUM
            ) TBL_MEM

            ON TBL.QUEUE_NUM=TBL_MEM.QUEUE_NUM

            ORDER BY TBL.HDID DESC
            LIMIT 20
        ");
        $stmt -> bindValue(':status1', $status);
        $stmt -> bindValue(':status2', $status);
        $stmt -> bindValue(':status3', $status);
        $data = $stmt -> execute();
    }
    else {
        echo "<h2>PENDING</h2><hr>";
        $stmt = $con -> prepare("
            SELECT 
            TBL.HDID, TBL.QUEUE_NUM, TBL.REQUESTOR_NAME, TBL.STATUS, TBL.PRIORITY, TBL.TRANSACTION_NUM,
            CASE WHEN (TBL_EMP.EMP IS NULL) OR (TBL_MEM.MEM IS NULL) THEN TBL.CLIENT_TYPE ELSE 'MEMBER AND EMPLOYER' END CLIENT_TYPE

            FROM (
                SELECT HDID, CLIENT_TYPE,
                QUEUE_NUM, REQUESTOR_NAME, STATUS, 
                PRIORITY, COUNT(ID) TRANSACTION_NUM
                FROM tbl_transaction 
                WHERE STATUS='PENDING' AND DATE(TRANSACTION_DATE)=CURRENT_DATE
                GROUP BY QUEUE_NUM
            ) TBL

            LEFT JOIN (
                SELECT QUEUE_NUM, CLIENT_TYPE EMP, STATUS 
                FROM tbl_transaction 
                WHERE CLIENT_TYPE='EMPLOYER' AND STATUS='PENDING' 
                AND DATE(TRANSACTION_DATE)=CURRENT_DATE
                GROUP BY QUEUE_NUM
            ) TBL_EMP

            ON TBL.QUEUE_NUM=TBL_EMP.QUEUE_NUM

            LEFT JOIN (
                SELECT QUEUE_NUM, CLIENT_TYPE MEM, STATUS
                FROM tbl_transaction 
                WHERE CLIENT_TYPE='MEMBER' AND STATUS='PENDING' 
                AND DATE(TRANSACTION_DATE)=CURRENT_DATE
                GROUP BY QUEUE_NUM
            ) TBL_MEM

            ON TBL.QUEUE_NUM=TBL_MEM.QUEUE_NUM

            ORDER BY TBL.HDID DESC
            LIMIT 20
        ");
        $data = $stmt -> execute();
    }

    if ($stmt -> rowCount() < 1) {
        echo '
        <div class="text-center" id="placeholder">
        <h3 class="text-muted">No Transactions Yet</h3>
            <img class="my-3 img-fluid mx-auto" src="'.STATIC_URL.'svg/open-box.svg" width="200">
        </div>';
    }

    while ($data = $stmt -> fetch()) {
        echo '
        <div class="d-flex p-3" id="card_'.$data['HDID'].'">
            <div class="pb-3 mb-0 lh-sm border-bottom w-100">
                <div class="d-flex justify-content-between">
                    <h3>#'.$data["QUEUE_NUM"].'</h3>
                    <a class="rounded-pill" href="client-details.php?hdid='.$data["HDID"].'&queue='.$data["QUEUE_NUM"].'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-box-arrow-in-right" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0v-2z"/>
                        <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                    </svg>
                    </a>
                </div>
                '.$data["REQUESTOR_NAME"].'<br>
                '.client_type($data["CLIENT_TYPE"]).'
                '.num_of_transactions($data["TRANSACTION_NUM"]).'
                '.priority_type($data["PRIORITY"]).'
            </div>
        </div>';
    }
?>