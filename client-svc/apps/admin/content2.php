<?php
    
    session_start();

    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/connect_db.php';
    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/security_check.php';
    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/utils/chk_login.php';

    if (!func::checkLoginState($con)) {
        header("Location: login.php");
        die();
    }

    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/query.php';

    $start_date = date('Y-m-d');
    $end_date = date('Y-m-d');
    
    $stmt = $con -> prepare("
    SELECT 
    (CASE WHEN (COUNT(TBLPENDING.QUEUE_NUM)) IS NULL THEN '0' ELSE COUNT(TBLPENDING.QUEUE_NUM) END ) 'Pending', 
    (CASE WHEN (COUNT(TBLONPROC.QUEUE_NUM)) IS NULL THEN '0' ELSE COUNT(TBLONPROC.QUEUE_NUM) END ) 'On Process', 
    (CASE WHEN (COUNT(TBLDONE.QUEUE_NUM)) IS NULL THEN '0' ELSE COUNT(TBLDONE.QUEUE_NUM) END ) 'Done', 
    (CASE WHEN (COUNT(TBLCANCELLED.QUEUE_NUM)) IS NULL THEN '0' ELSE COUNT(TBLCANCELLED.QUEUE_NUM) END ) 'Cancelled'

    FROM (
        SELECT QUEUE_NUM FROM tbl_transaction GROUP BY QUEUE_NUM
    ) TBL
    
    LEFT JOIN (
        SELECT QUEUE_NUM FROM tbl_transaction WHERE STATUS='PENDING' 
        AND DATE(TRANSACTION_DATE)=CURRENT_DATE
        GROUP BY QUEUE_NUM
    ) TBLPENDING ON TBL.QUEUE_NUM=TBLPENDING.QUEUE_NUM

    LEFT JOIN (
        SELECT QUEUE_NUM FROM tbl_transaction WHERE STATUS='ON PROCESS' 
        AND DATE(TRANSACTION_DATE)=CURRENT_DATE
        GROUP BY QUEUE_NUM
    ) TBLONPROC ON TBL.QUEUE_NUM=TBLONPROC.QUEUE_NUM

    LEFT JOIN (
        SELECT QUEUE_NUM FROM tbl_transaction WHERE STATUS='DONE' 
        AND DATE(TRANSACTION_DATE)=CURRENT_DATE
        GROUP BY QUEUE_NUM
    ) TBLDONE ON TBL.QUEUE_NUM=TBLDONE.QUEUE_NUM

    LEFT JOIN (
        SELECT QUEUE_NUM FROM tbl_transaction WHERE STATUS='CANCELLED' 
        AND DATE(TRANSACTION_DATE)=CURRENT_DATE
        GROUP BY QUEUE_NUM
    ) TBLCANCELLED ON TBL.QUEUE_NUM=TBLCANCELLED.QUEUE_NUM
    
    ");

    $stmt -> execute();
    $data = $stmt -> fetch();
    
    if ($data) {
        echo "
        <ol class=\"list-group list-group mb-2\">
        <li class=\"list-group-item d-flex justify-content-between align-items-start\">
            <div class=\"ms-2 me-auto\">
                <a href=\"transactions.php?status=PENDING\" id=\"pending-link\" class=\"card-link\">Pending</a>
            </div>
            <span class=\"badge bg-primary rounded-pill\">{$data[0]}</span>
        </li>
        <li class=\"list-group-item d-flex justify-content-between align-items-start\">
            <div class=\"ms-2 me-auto\">
                <a href=\"transactions.php?status=ON+PROCESS\" id=\"on-process-link\" class=\"card-link\">On Process</a>
            </div>
            <span class=\"badge bg-primary rounded-pill\">{$data[1]}</span>
        </li>
        <li class=\"list-group-item d-flex justify-content-between align-items-start\">
            <div class=\"ms-2 me-auto\">
                <a href=\"transactions.php?status=DONE\" id=\"done-link\" class=\"card-link\">Done</a>
            </div>
            <span class=\"badge bg-primary rounded-pill\">{$data[2]}</span>
        </li>
        <li class=\"list-group-item d-flex justify-content-between align-items-start\">
            <div class=\"ms-2 me-auto\">
                <a href=\"transactions.php?status=CANCELLED\" id=\"cancelled-link\" class=\"card-link\">Cancelled</a>
            </div>
            <span class=\"badge bg-primary rounded-pill\">{$data[3]}</span>
        </li>
        </ol>";
    }

    $stmt = $con -> prepare("
    SELECT 
    (CASE WHEN (TBLMDR.MDR) IS NULL THEN '0' ELSE TBLMDR.MDR END ) 'MDR', 
    (CASE WHEN (TBLID.ID) IS NULL THEN '0' ELSE TBLID.ID END ) 'ID', 
    (CASE WHEN (TBLPC.PC) IS NULL THEN '0' ELSE TBLPC.PC END) 'Premium Contrib',
    (CASE WHEN (TBLUM.UM) IS NULL THEN '0' ELSE TBLUM.UM END) 'Member Updating'

    FROM (
        SELECT TRANSACTION_DATE, (CASE WHEN COUNT(HDID) IS NULL THEN '0' ELSE COUNT(HDID) END) CLIENTS FROM `tbl_transaction` 
        WHERE STATUS='PENDING' AND CLIENT_TYPE='MEMBER' AND TRANSACTION_DATE BETWEEN '$start_date' AND '$end_date' 
        GROUP BY TRANSACTION_DATE 
        ORDER BY TRANSACTION_DATE ASC
    ) TBLDATE

    LEFT JOIN (
        SELECT TRANSACTION_DATE, (CASE WHEN COUNT(ID) IS NULL THEN '0' ELSE COUNT(ID) END) MDR FROM `tbl_transaction` 
        WHERE TRANSACTION_DESC LIKE ('%MEMBER DATA RECORD%') AND STATUS='PENDING' AND CLIENT_TYPE='MEMBER'
        AND TRANSACTION_DATE BETWEEN '$start_date' AND '$end_date' 
        GROUP BY TRANSACTION_DATE 
        ORDER BY TRANSACTION_DATE ASC
    ) TBLMDR ON TBLDATE.TRANSACTION_DATE=TBLMDR.TRANSACTION_DATE 

    LEFT JOIN (
        SELECT TRANSACTION_DATE, (CASE WHEN COUNT(ID) IS NULL THEN '0' ELSE COUNT(ID) END) ID FROM `tbl_transaction` 
        WHERE TRANSACTION_DESC LIKE ('%PNC/ID CARD%') AND STATUS='PENDING' AND CLIENT_TYPE='MEMBER'
        AND TRANSACTION_DATE BETWEEN '$start_date' AND '$end_date' 
        GROUP BY TRANSACTION_DATE 
        ORDER BY TRANSACTION_DATE ASC
    ) TBLID ON TBLDATE.TRANSACTION_DATE=TBLID.TRANSACTION_DATE 

    LEFT JOIN (
        SELECT TRANSACTION_DATE, (CASE WHEN COUNT(ID) IS NULL THEN '0' ELSE COUNT(ID) END) PC FROM `tbl_transaction` 
        WHERE TRANSACTION_DESC LIKE ('%PREMIUM CONTRIBUTION%') AND STATUS='PENDING' AND CLIENT_TYPE='MEMBER'
        AND TRANSACTION_DATE BETWEEN '$start_date' AND '$end_date' 
        GROUP BY TRANSACTION_DATE 
        ORDER BY TRANSACTION_DATE ASC
    ) TBLPC ON TBLDATE.TRANSACTION_DATE=TBLPC.TRANSACTION_DATE

    LEFT JOIN (
        SELECT TRANSACTION_DATE, (CASE WHEN SUM(MEMBER_UPDATE_COUNT) IS NULL THEN '0' ELSE SUM(MEMBER_UPDATE_COUNT) END) UM FROM `tbl_transaction` 
        WHERE TRANSACTION_DESC LIKE ('%MEMBER UPDATING%') AND STATUS='PENDING' AND CLIENT_TYPE='MEMBER'
        AND TRANSACTION_DATE BETWEEN '$start_date' AND '$end_date' 
        GROUP BY TRANSACTION_DATE 
        ORDER BY TRANSACTION_DATE ASC
    ) TBLUM ON TBLDATE.TRANSACTION_DATE=TBLUM.TRANSACTION_DATE 
    ");

    $stmt -> execute();
    $data = $stmt -> fetch();

    if ($data) {
        echo "
        <h6>Pending Member Transactions</h6>
        <ol class=\"list-group list-group mb-2\">
            <li class=\"list-group-item d-flex justify-content-between align-items-start\">
                <div class=\"ms-2 me-auto\">Member Data Record</div>
                <span class=\"badge bg-primary rounded-pill\">{$data[0]}</span>
            </li>
            <li class=\"list-group-item d-flex justify-content-between align-items-start\">
                <div class=\"ms-2 me-auto\">PNC/ID Card</div>
                <span class=\"badge bg-primary rounded-pill\">{$data[1]}</span>
            </li>
            <li class=\"list-group-item d-flex justify-content-between align-items-start\">
                <div class=\"ms-2 me-auto\">Premium Contribution</div>
                <span class=\"badge bg-primary rounded-pill\">{$data[2]}</span>
            </li>
            <li class=\"list-group-item d-flex justify-content-between align-items-start\">
                <div class=\"ms-2 me-auto\">Member Updating</div>
                <span class=\"badge bg-primary rounded-pill\">{$data[3]}</span>
            </li>
        </ol>
        ";
    }
    else {
        echo "
        <h6>Pending Member Transactions</h6>
        <ol class=\"list-group list-group mb-2\">
        <li class=\"list-group-item text-center text-muted\">
            <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"40\" height=\"40\" fill=\"currentColor\" class=\"bi bi-calendar2-x me-2\" viewBox=\"0 0 16 16\">
            <path d=\"M6.146 8.146a.5.5 0 0 1 .708 0L8 9.293l1.146-1.147a.5.5 0 1 1 .708.708L8.707 10l1.147 1.146a.5.5 0 0 1-.708.708L8 10.707l-1.146 1.147a.5.5 0 0 1-.708-.708L7.293 10 6.146 8.854a.5.5 0 0 1 0-.708z\"/>
            <path d=\"M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H2z\"/>
            <path d=\"M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5V4z\"/>
            </svg>
            No pending member transactions for this day
        </li>
        </ol>
        ";
    }

    $stmt = $con -> prepare("
    SELECT 
    (CASE WHEN (TBLMDR.MDR) IS NULL THEN '0' ELSE TBLMDR.MDR END ) 'MDR', 
    (CASE WHEN (TBLID.ID) IS NULL THEN '0' ELSE TBLID.ID END ) 'ID', 
    (CASE WHEN (TBLUM.UM) IS NULL THEN '0' ELSE TBLUM.UM END) 'Member Updating',
    (CASE WHEN (TBLRU.RU) IS NULL THEN '0' ELSE TBLRU.RU END) 'Record Updating',
    (CASE WHEN (TBLRQ.RQ) IS NULL THEN '0' ELSE TBLRQ.RQ END) 'Request Certificate'
    
    FROM (
        SELECT TRANSACTION_DATE, (CASE WHEN COUNT(HDID) IS NULL THEN '0' ELSE COUNT(HDID) END) CLIENTS FROM `tbl_transaction` 
        WHERE STATUS='PENDING' AND CLIENT_TYPE='EMPLOYER' AND TRANSACTION_DATE BETWEEN '$start_date' AND '$end_date' 
        GROUP BY TRANSACTION_DATE 
        ORDER BY TRANSACTION_DATE ASC
    ) TBLDATE
    
    LEFT JOIN (
        SELECT TRANSACTION_DATE, (CASE WHEN COUNT(ID) IS NULL THEN '0' ELSE COUNT(ID) END) MDR FROM `tbl_transaction` 
        WHERE TRANSACTION_DESC LIKE ('%MEMBER DATA RECORD%') AND STATUS='PENDING' AND CLIENT_TYPE='EMPLOYER'
        AND TRANSACTION_DATE BETWEEN '$start_date' AND '$end_date' 
        GROUP BY TRANSACTION_DATE 
        ORDER BY TRANSACTION_DATE ASC
    ) TBLMDR ON TBLDATE.TRANSACTION_DATE=TBLMDR.TRANSACTION_DATE 

    LEFT JOIN (
        SELECT TRANSACTION_DATE, (CASE WHEN COUNT(ID) IS NULL THEN '0' ELSE COUNT(ID) END) ID FROM `tbl_transaction` 
        WHERE TRANSACTION_DESC LIKE ('%PNC/ID CARD%') AND STATUS='PENDING' AND CLIENT_TYPE='EMPLOYER'
        AND TRANSACTION_DATE BETWEEN '$start_date' AND '$end_date' 
        GROUP BY TRANSACTION_DATE 
        ORDER BY TRANSACTION_DATE ASC
    ) TBLID ON TBLDATE.TRANSACTION_DATE=TBLID.TRANSACTION_DATE 

    LEFT JOIN (
        SELECT TRANSACTION_DATE, (CASE WHEN SUM(MEMBER_UPDATE_COUNT) IS NULL THEN '0' ELSE SUM(MEMBER_UPDATE_COUNT) END) UM FROM `tbl_transaction` 
        WHERE TRANSACTION_DESC LIKE ('%MEMBERS UPDATING%') AND STATUS='PENDING' AND CLIENT_TYPE='EMPLOYER'
        AND TRANSACTION_DATE BETWEEN '$start_date' AND '$end_date' 
        GROUP BY TRANSACTION_DATE 
        ORDER BY TRANSACTION_DATE ASC
    ) TBLUM ON TBLDATE.TRANSACTION_DATE=TBLUM.TRANSACTION_DATE 

    LEFT JOIN (
        SELECT TRANSACTION_DATE, (CASE WHEN COUNT(ID) IS NULL THEN '0' ELSE COUNT(ID) END) RU FROM `tbl_transaction` 
        WHERE TRANSACTION_DESC LIKE ('%RECORD UPDATING%') AND STATUS='PENDING' AND CLIENT_TYPE='EMPLOYER'
        AND TRANSACTION_DATE BETWEEN '$start_date' AND '$end_date' 
        GROUP BY TRANSACTION_DATE 
        ORDER BY TRANSACTION_DATE ASC
    ) TBLRU ON TBLDATE.TRANSACTION_DATE=TBLRU.TRANSACTION_DATE 

    LEFT JOIN (
        SELECT TRANSACTION_DATE, (CASE WHEN COUNT(ID) IS NULL THEN '0' ELSE COUNT(ID) END) RQ FROM `tbl_transaction` 
        WHERE TRANSACTION_DESC LIKE ('%REQUEST CERTIFICATE FOR BUSINESS PERMIT%') AND STATUS='PENDING' AND CLIENT_TYPE='EMPLOYER'
        AND TRANSACTION_DATE BETWEEN '$start_date' AND '$end_date' 
        GROUP BY TRANSACTION_DATE 
        ORDER BY TRANSACTION_DATE ASC
    ) TBLRQ ON TBLDATE.TRANSACTION_DATE=TBLRQ.TRANSACTION_DATE
    ");

    $stmt -> execute();
    $data = $stmt -> fetch();

    if ($data) {
        echo "
        <h6>Pending Employer Transactions</h6>
        <ol class=\"list-group list-group mb-2\">
            <li class=\"list-group-item d-flex justify-content-between align-items-start\">
                <div class=\"ms-2 me-auto\">Member Data Record</div>
                <span class=\"badge bg-primary rounded-pill\">{$data[0]}</span>
            </li>
            <li class=\"list-group-item d-flex justify-content-between align-items-start\">
                <div class=\"ms-2 me-auto\">PNC/ID Card</div>
                <span class=\"badge bg-primary rounded-pill\">{$data[1]}</span>
            </li>
            <li class=\"list-group-item d-flex justify-content-between align-items-start\">
                <div class=\"ms-2 me-auto\">Members Updating</div>
                <span class=\"badge bg-primary rounded-pill\">{$data[2]}</span>
            </li>
            <li class=\"list-group-item d-flex justify-content-between align-items-start\">
                <div class=\"ms-2 me-auto\">Record Updating</div>
                <span class=\"badge bg-primary rounded-pill\">{$data[3]}</span>
            </li>
            <li class=\"list-group-item d-flex justify-content-between align-items-start\">
                <div class=\"ms-2 me-auto\">Request Cert. for Business Permit</div>
                <span class=\"badge bg-primary rounded-pill\">{$data[4]}</span>
            </li>
        </ol>";
    }
    else {
        echo "
        <h6>Pending Employer Transactions</h6>
        <ol class=\"list-group list-group mb-2\">
        <li class=\"list-group-item text-center text-muted\">
            <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"40\" height=\"40\" fill=\"currentColor\" class=\"bi bi-calendar2-x me-2\" viewBox=\"0 0 16 16\">
            <path d=\"M6.146 8.146a.5.5 0 0 1 .708 0L8 9.293l1.146-1.147a.5.5 0 1 1 .708.708L8.707 10l1.147 1.146a.5.5 0 0 1-.708.708L8 10.707l-1.146 1.147a.5.5 0 0 1-.708-.708L7.293 10 6.146 8.854a.5.5 0 0 1 0-.708z\"/>
            <path d=\"M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H2z\"/>
            <path d=\"M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5V4z\"/>
            </svg>
            No pending employer transactions for this day
        </li>
        </ol>
        ";
    }
?>