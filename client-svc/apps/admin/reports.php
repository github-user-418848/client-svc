<?php
    session_start();

    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/connect_db.php';
    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/utils/chk_login.php';

    if (!func::checkLoginState($con)) {
        header("Location: login.php");
        die();
    }

    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/snippets/block-content.php';
    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/query.php';

    
    $stmt = $con -> prepare('SELECT * FROM tbl_users WHERE EMPLOYEE_NO=:emp_no');
    $stmt -> bindValue(':emp_no', $_SESSION["username"]);
    $stmt -> execute();

    $row = $stmt -> fetch(PDO::FETCH_ASSOC);
    $_SESSION['user_role'] = $row['USER_ROLE'];

    
?>
<ul class="nav justify-content-center">
    <li class="nav-item">
        <a class="nav-link border-bottom border-dark" href="reports.php">
        Reports
        </a>
    </li>
    <li class="nav-item" id="transaction">
        <a class="nav-link" href="transactions.php">
        Transactions
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="my-account.php">
        Account
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="logout.php">
        Logout
        </a>
    </li>
</ul>
<?php

    if (isset($_GET["start_date"]) && isset($_GET["end_date"]) && isset($_GET["filter"])) {
        input::$redirect_location = "reports.php";
        $start_date = input::checkDate(date('Y-m-d', strtotime($_GET["start_date"])));
        $end_date = input::checkDate(date('Y-m-d', strtotime($_GET["end_date"])));
    }
    else {
        $start_date = date('Y-m-d', strtotime(date("Y-m")."-1"));
        $end_date = date('Y-m-d', strtotime(date("Y-m")."-".cal_days_in_month(CAL_GREGORIAN, date("m"), date("Y"))));
    }

    $frontliner_qry = "";
    if ($_SESSION["user_role"] == "FRONTLINER") {
        $frontliner_qry = "AND FRONTLINE_ID={$_SESSION['username']}";
    }
    
    $stmt = $con -> query("
        SELECT 
        TBLNOF.TRANSACTION_DATE 'Date', 
        TBLNOF.CLIENTS 'Clients',  
        SUM(
            (CASE WHEN (TBLEM.NEWMEMBER) IS NULL THEN '0' ELSE TBLEM.NEWMEMBER END ) + 
            (CASE WHEN (TBLEE.NEWEMPLOYERS) IS NULL THEN '0' ELSE TBLEE.NEWEMPLOYERS END ) +
            (CASE WHEN (TBLUM.MEMBERUPDATES) IS NULL THEN '0' ELSE TBLUM.MEMBERUPDATES END ) +
            (CASE WHEN (TBLUE.EMPLOYERSUPDATES) IS NULL THEN '0' ELSE TBLUE.EMPLOYERSUPDATES END ) +
            (CASE WHEN (TBLMDR.MDR) IS NULL THEN '0' ELSE TBLMDR.MDR END ) + 
            (CASE WHEN (TBLID.IDCOUNT) IS NULL THEN '0' ELSE TBLID.IDCOUNT END ) +
            (CASE WHEN (TBLPC.CONTRIBUTION) IS NULL THEN '0' ELSE TBLPC.CONTRIBUTION END)
        ) 'Number of Total Transactions', 
        (CASE WHEN (TBLEM.NEWMEMBER) IS NULL THEN '0' ELSE TBLEM.NEWMEMBER END ) 'New Member', 
        (CASE WHEN (TBLEE.NEWEMPLOYERS) IS NULL THEN '0' ELSE TBLEE.NEWEMPLOYERS END ) 'New Employer', 
        (CASE WHEN (TBLUM.MEMBERUPDATES) IS NULL THEN '0' ELSE TBLUM.MEMBERUPDATES END ) 'Member', 
        (CASE WHEN (TBLUE.EMPLOYERSUPDATES) IS NULL THEN '0' ELSE TBLUE.EMPLOYERSUPDATES END ) 'Employer', 
        (CASE WHEN (TBLMDR.MDR) IS NULL THEN '0' ELSE TBLMDR.MDR END ) 'MDR', 
        (CASE WHEN (TBLID.IDCOUNT) IS NULL THEN '0' ELSE TBLID.IDCOUNT END ) 'ID', 
        (CASE WHEN (TBLPC.CONTRIBUTION) IS NULL THEN '0' ELSE TBLPC.CONTRIBUTION END) 'Premium Contrib'

        FROM (
            SELECT TRANSACTION_DATE, (CASE WHEN COUNT(HDID) IS NULL THEN '0' ELSE COUNT(HDID) END) CLIENTS FROM `tbl_transaction` 
            WHERE STATUS='DONE' $frontliner_qry AND TRANSACTION_DATE BETWEEN '$start_date' AND '$end_date' 
            GROUP BY TRANSACTION_DATE ORDER BY TRANSACTION_DATE ASC
        ) TBLNOF

        LEFT JOIN (
            SELECT TRANSACTION_DATE, (CASE WHEN COUNT(ID) IS NULL THEN '0' ELSE COUNT(ID) END) NEWMEMBER 
            FROM `tbl_transaction` WHERE PIN_PEN='NEW_MEMBER' $frontliner_qry AND STATUS='DONE' AND TRANSACTION_DATE BETWEEN '$start_date' AND '$end_date' 
            GROUP BY TRANSACTION_DATE ORDER BY TRANSACTION_DATE ASC
        ) TBLEM ON TBLNOF.TRANSACTION_DATE=TBLEM.TRANSACTION_DATE 

        LEFT JOIN (
            SELECT TRANSACTION_DATE, (CASE WHEN COUNT(ID) IS NULL THEN '0' ELSE COUNT(ID) END) NEWEMPLOYERS 
            FROM `tbl_transaction` WHERE PIN_PEN='NEW_EMPLOYER' $frontliner_qry AND STATUS='DONE' AND TRANSACTION_DATE BETWEEN '$start_date' AND '$end_date' 
            GROUP BY TRANSACTION_DATE 
            ORDER BY TRANSACTION_DATE ASC

        ) TBLEE ON TBLNOF.TRANSACTION_DATE=TBLEE.TRANSACTION_DATE 

        LEFT JOIN (
            SELECT TRANSACTION_DATE, (CASE WHEN SUM(MEMBER_UPDATE_COUNT) IS NULL THEN '0' ELSE SUM(MEMBER_UPDATE_COUNT) END) MEMBERUPDATES FROM `tbl_transaction` 
            WHERE TRANSACTION_DESC LIKE ('%MEMBER UPDATING%') $frontliner_qry AND STATUS='DONE' AND TRANSACTION_DATE 
            BETWEEN '$start_date' AND '$end_date' 
            GROUP BY TRANSACTION_DATE 
            ORDER BY TRANSACTION_DATE ASC
        ) TBLUM ON TBLNOF.TRANSACTION_DATE=TBLUM.TRANSACTION_DATE 

        LEFT JOIN (
            SELECT TRANSACTION_DATE, (CASE WHEN COUNT(ID) IS NULL THEN '0' ELSE COUNT(ID) END) EMPLOYERSUPDATES FROM `tbl_transaction` 
            WHERE CLIENT_TYPE='EMPLOYER' AND TRANSACTION_DESC LIKE ('%RECORD UPDATING%') $frontliner_qry AND STATUS='DONE' AND TRANSACTION_DATE 
            BETWEEN '$start_date' AND '$end_date' 
            GROUP BY TRANSACTION_DATE 
            ORDER BY TRANSACTION_DATE ASC
        ) TBLUE ON TBLNOF.TRANSACTION_DATE=TBLUE.TRANSACTION_DATE 

        LEFT JOIN (
            SELECT TRANSACTION_DATE, (CASE WHEN COUNT(ID) IS NULL THEN '0' ELSE COUNT(ID) END) MDR FROM `tbl_transaction` 
            WHERE TRANSACTION_DESC LIKE ('%MEMBER DATA RECORD%') $frontliner_qry AND STATUS='DONE' AND TRANSACTION_DATE 
            BETWEEN '$start_date' AND '$end_date' 
            GROUP BY TRANSACTION_DATE 
            ORDER BY TRANSACTION_DATE ASC
        ) TBLMDR ON TBLNOF.TRANSACTION_DATE=TBLMDR.TRANSACTION_DATE 

        LEFT JOIN (
            SELECT TRANSACTION_DATE, (CASE WHEN COUNT(ID) IS NULL THEN '0' ELSE COUNT(ID) END) IDCOUNT FROM `tbl_transaction` 
            WHERE TRANSACTION_DESC LIKE ('%PNC/ID CARD%') $frontliner_qry AND STATUS='DONE' AND TRANSACTION_DATE 
            BETWEEN '$start_date' AND '$end_date' 
            GROUP BY TRANSACTION_DATE 
            ORDER BY TRANSACTION_DATE ASC
        ) TBLID ON TBLNOF.TRANSACTION_DATE=TBLID.TRANSACTION_DATE 

        LEFT JOIN (
            SELECT TRANSACTION_DATE, (CASE WHEN COUNT(ID) IS NULL THEN '0' ELSE COUNT(ID) END) CONTRIBUTION FROM `tbl_transaction` 
            WHERE TRANSACTION_DESC LIKE ('%PREMIUM CONTRIBUTION%') $frontliner_qry AND STATUS='DONE' AND TRANSACTION_DATE 
            BETWEEN '$start_date' AND '$end_date' 
            GROUP BY TRANSACTION_DATE 
            ORDER BY TRANSACTION_DATE ASC
        ) TBLPC ON TBLNOF.TRANSACTION_DATE=TBLPC.TRANSACTION_DATE

        GROUP BY TBLNOF.TRANSACTION_DATE
    ");
    $stmt -> execute();
    
    $tbl = "";
    $count = 0;
    
    $num_clients = 0;
    $num_transactions = 0;
    $num_new_member = 0;
    $num_new_employer = 0;
    $num_member = 0;
    $num_employer = 0;
    $num_mdr = 0;
    $num_pnc = 0;
    $num_contrib = 0;

    while ($data = $stmt -> fetch()) {
        $count++;
        $tbl .= "
        <tr>
            <th scope=\"row\">{$count}</th>
            <td>".date("M d", strtotime($data[0]))."</td>
            <td>{$data[1]}</td>
            <td>{$data[2]}</td>
            <td>{$data[3]}</td>
            <td>{$data[4]}</td>
            <td>{$data[5]}</td>
            <td>{$data[6]}</td>
            <td>{$data[7]}</td>
            <td>{$data[8]}</td>
            <td>{$data[9]}</td>
            <td></td>
        </tr>";
        $num_clients += $data[1];
        $num_transactions += $data[2];
        $num_new_member += $data[3];
        $num_new_employer += $data[4];
        $num_member += $data[5];
        $num_employer += $data[6];
        $num_mdr += $data[7];
        $num_pnc += $data[8];
        $num_contrib += $data[9];
    }

    
    $tbl .= "
    <tr>
        <th colspan=\"2\">Total</th>
        <td>$num_clients</td>
        <td>$num_transactions</td>
        <td>$num_new_member</td>
        <td>$num_new_employer</td>
        <td>$num_member</td>
        <td>$num_employer</td>
        <td>$num_mdr</td>
        <td>$num_pnc</td>
        <td>$num_contrib</td>
        <td></td>
    </tr>";

?>
<main class="mx-auto my-3 bg-white border p-4">
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h5>Total Transactions (<?php echo date("M d, Y", strtotime($start_date))?> - <?php echo date("M d, Y", strtotime($end_date))?>)</h5>
    <a class="btn btn-outline-dark" target="_blank" href="/client-svc/apps/print-pdf/gen-report.php<?php echo "?start_date=$start_date&end_date=$end_date"?>">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
        <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
        <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
    </svg> Print Report</a>
</div>
<form method="get">
    <div class="input-group mb-3 justify-content-center flex-column flex-md-row">
        <div class="form-floating col-md-5">
            <input class="form-control" type="date" name="start_date" id="start_date" placeholder="" <?php echo "value='$start_date'"; ?>>
            <label for="start_date">Start Date</label>
            <div class="invalid-feedback">
                This field is required.
            </div>
        </div>
        <div class="form-floating col-md-5">
            <input class="form-control" type="date" name="end_date" id="end_date" placeholder="" <?php echo "value='$end_date'"; ?>>
            <label for="end_date">End Date</label>
            <div class="invalid-feedback">
                This field is required.
            </div>
        </div>
        <input type="submit" value="Filter Date" name="filter" class="btn btn-dark">
    </div>
</form>
<canvas id="chart" height="175"></canvas>
<div class="table-responsive text-nowrap mt-2">
    <table class="table caption-top rounded-2">
        <caption>Reports for <?php echo date("M d, Y", strtotime($start_date))?> - <?php echo date("M d, Y", strtotime($end_date))?></caption>
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Date</th>
                <th scope="col">Number of Clients</th>
                <th scope="col">Transactions Done</th>
                <th scope="col">New Member</th>
                <th scope="col">New Employer</th>
                <th scope="col">Member</th>
                <th scope="col">Employer</th>
                <th scope="col">MDR</th>
                <th scope="col">PNC/ID Card</th>
                <th scope="col">Contribution History</th>
            </tr>
        </thead>
        <tbody>
            <?php echo $tbl; ?>
        </tbody>
    </table>
</div>
</main>
<script src="/client-svc/static/js/chart.min.js"></script>
<script nonce="<?php echo $_SESSION["random-nonce"]; ?>">
    
    Chart.defaults.font.family = 'Rubik';
    Chart.defaults.plugins.legend.display = false;

    const labels = [
        'New Member',
        'New Employer',
        'Member',
        'Employer',
        'MDR',
        'PNC/ID Card',
        'Contribution History',
    ];
    const data = {
        labels: labels,
        datasets: [{
            label: 'Total Transactions',
            backgroundColor: '#00502b69',
            data: [<?php echo 
                "$num_new_member,
                $num_new_employer,
                $num_member,
                $num_employer,
                $num_mdr,
                $num_pnc,
                $num_contrib"; 
            ?>],
            borderColor: '#00502a',
            fill: true,
            lineTension: 0,
        }]
    };
    const config = {
        type: 'line',
        data: data,
        options: {
            scales: {
                yAxes: [{
                ticks: {
                    beginAtZero: false
                }
                }]
            },
            legend: {
                display: false
            },
            tooltips: {
                enabled: false
            }
        }
    };
    var myChart = new Chart(
        document.getElementById('chart'),
        config
    );
</script>
<?php
    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/snippets/endblock.php';