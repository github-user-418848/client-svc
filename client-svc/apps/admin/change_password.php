<?php
    session_start();

    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/utils/connect_db.php';
    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/utils/chk_login.php';

    if (!func::checkLoginState($con)) {
        header("Location: login.php");
        die();
    }

    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(16));
    }

    function clean ($input) {
        $clean_data = trim($input);
        $clean_data = stripslashes($input);
        $clean_data = htmlspecialchars($input);
        return $clean_data;
    }

    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/snippets/block-content.php';

    echo '
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

    <form method="post" class="bg-white border p-4 mx-auto my-3" style="max-width: 500px;" id="form">
    <h2 class="text-center">Change Password</h2>
        <div class="form-floating mt-2 mb-3">
            <input class="form-control" type="password" name="passwd" id="passwd" placeholder="">
            <label for="passwd">Password</label>
            <div class="invalid-feedback">Please enter your password</div>
        </div>
        <div class="form-floating mt-2 mb-3">
            <input class="form-control" type="password" name="new_passwd" id="new_passwd" placeholder="">
            <label for="new_passwd">New Password</label>
            <div class="invalid-feedback">This field is required</div>
        </div>
        <div class="form-floating mt-2 mb-3">
            <input class="form-control" type="password" name="confirm_new_passwd" id="confirm_new_passwd" placeholder="">
            <label for="confirm_new_passwd">Confirm New Password</label>
            <div class="invalid-feedback">This field is required</div>
        </div>
        <input type="hidden" name="csrf_token" id="csrf_token" value="'.$_SESSION['csrf_token'].'">
        <input type="submit" value="Change Password" class="btn btn-dark w-100 btn-lg mb-3" name="change_passwd">
    </form>
    <script type="text/javascript" src="/client-svc/static/js/form-validation.js"></script>
    ';

    // $password = password_hash("30702318", PASSWORD_BCRYPT, array('cost' => 13));
    // $stmt = $con -> prepare("UPDATE tbl_users SET PASSWORD='$password' WHERE ID=".$_SESSION['user_id']) -> execute();
                            
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST["change_passwd"]) && isset($_POST['csrf_token']) && ($_POST['csrf_token'] == $_POST['csrf_token'])) {

            $stmt = $con -> prepare("SELECT * FROM tbl_users WHERE EMPLOYEE_NO=".$_SESSION['username']);
            $stmt -> execute();
            $data = $stmt -> fetch();

            if ($data) {
                $hashed_passwd = $data["PASSWORD"];
                if (!empty($_POST["passwd"]) && !empty($_POST["new_passwd"]) && !empty($_POST["confirm_new_passwd"])) {
                    $passwd = clean($_POST["passwd"]);
                    $new_passwd = clean($_POST["new_passwd"]);
                    $confirm_new_passwd = clean($_POST["confirm_new_passwd"]);
                    if (password_verify($passwd, $hashed_passwd)) {
                        if ($new_passwd === $confirm_new_passwd) {
                            if (strlen($new_passwd) > 8) {
                                    $hash_new_passwd = password_hash($new_passwd, PASSWORD_BCRYPT, array('cost' => 13));
                                    $stmt = $con -> prepare("UPDATE tbl_users SET PASSWORD='$hash_new_passwd' WHERE ID=".$_SESSION['username']) -> execute();
                                    $_SESSION['msg'] = '<div class="alert alert-success alert-dismissible fade show mx-auto text-center my-4" role="alert" style="max-width: 800px;"><i class="fa fa-info-circle fa-lg me-2" aria-hidden="true"></i>You have successfully changed your password<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                    header("Location: change_password.php");
                            }
                            else {
                                $_SESSION['msg'] = '<div class="alert alert-danger alert-dismissible fade show mx-auto text-center my-4" role="alert" style="max-width: 800px;"><i class="fa fa-exclamation-triangle fa-lg me-2" aria-hidden="true"></i>Password must be greater than 8 characters<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                header("Location: change_password.php");
                            }
                        }
                        else {
                            $_SESSION['msg'] = '<div class="alert alert-danger alert-dismissible fade show mx-auto text-center my-4" role="alert" style="max-width: 800px;"><i class="fa fa-exclamation-triangle fa-lg me-2" aria-hidden="true"></i>The two password fields doesn\'t match<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                            header("Location: change_password.php");
                        }
                    }
                    else {
                        $_SESSION['msg'] = '<div class="alert alert-danger alert-dismissible fade show mx-auto text-center my-4" role="alert" style="max-width: 800px;"><i class="fa fa-exclamation-triangle fa-lg me-2" aria-hidden="true"></i>Incorrect password<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                        header("Location: change_password.php");
                    }
                }
            }
        }
    }
    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/snippets/endblock.php';