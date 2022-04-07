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

    $data = query::pdoSelectQuery("SELECT * FROM tbl_users WHERE EMPLOYEE_NO=:id LIMIT 1", array(":id" => $_SESSION["username"]), $con);
    
?>

<ul class="nav justify-content-center">
    <li class="nav-item">
        <a class="nav-link" href="reports.php">
        Reports
        </a>
    </li>
    <li class="nav-item" id="transaction">
        <a class="nav-link" href="transactions.php">
        Transactions
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link border-bottom border-dark" href="my-account.php">
        Account
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="logout.php">
        Logout
        </a>
    </li>
</ul>

<main class="mx-auto my-3 bg-white border p-3" style="max-width: 900px">
    ID Number<br>
    <h3><?php echo "#".$data["EMPLOYEE_NO"]; ?></h3>
    <div class="row">
        <div class="col-sm-4">
            Last Name<br>
            <h4><?php echo $data["LASTNAME"]; ?></h4>
        </div>
        <div class="col-sm-4">
            Middle Name<br>
            <h4><?php echo $data["MIDDLENAME"]; ?></h4>
        </div>
        <div class="col-sm-4">
            First Name<br>
            <h4><?php echo $data["FIRSTNAME"]; ?></h4>
        </div>
        <div class="col-sm-4">
            Counter Number<br>
            <h4><?php echo $data["COUNTER_NUM"]; ?></h4>
        </div>
        <div class="col-sm-4">
            User Role<br>
            <h4><?php echo $data["USER_ROLE"]; ?></h4>
        </div>
    </div>
    <div class="d-flex justify-content-end">
        <a class="mt-3 btn btn-dark" href="change_password.php">Change Password</a>
    </div>
</main>

<?php
    
    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/snippets/endblock.php';