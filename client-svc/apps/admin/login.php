<?php
    session_start();

    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/utils/connect_db.php';
    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/utils/chk_login.php';

    if (func::checkLoginState($con)) {
        header("Location: transactions.php");
        die();
    }

    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(16));
    }

    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/snippets/block-content.php';
    
?>
<h2 class="text-center">Frontline Login</h2>
<form class="mx-auto" method="post" style="max-width: 375px;" id="form">
    <div class="text-center">
    <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" fill="currentColor" class="bi bi-box-arrow-in-right text-dark" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0v-2z"/>
        <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
    </svg></div>
    <div class="form-floating">
        <input class="form-control mb-2" type="text" name="counter_num" id="counter_num" placeholder="">
        <label for="counter_num">Counter Number</label>
        <div class="invalid-feedback" id="invalid-feedback-num"></div>
    </div>
    <div class="form-floating">
        <input class="form-control mb-2" type="text" name="id_num" id="id_num" placeholder="">
        <label for="id_num">ID Number</label>
        <div class="invalid-feedback" id="invalid-feedback-num-0"></div>
    </div>
    <div class="form-floating">
        <input class="form-control mb-2" type="password" name="passwd" id="passwd" placeholder="">
        <label for="passwd">Password</label>
        <div class="invalid-feedback">This field is required</div>
    </div>
    <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <input type="submit" value="Login" class="btn btn-dark w-100 btn-lg mb-3" name="login">
</form>
<script type="text/javascript" src="<?php echo STATIC_URL; ?>js/form-validation.js"></script>
<?php
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST['csrf_token']) && !empty($_POST['csrf_token']) && ($_POST['csrf_token'] == $_SESSION['csrf_token'])) {
            if (!empty($_POST['id_num']) && !empty($_POST['passwd']) && !empty($_POST['counter_num']) && isset($_POST['login'])) {
                function clean ($input) {
                    $clean_data = trim($input);
                    $clean_data = stripslashes($input);
                    $clean_data = htmlspecialchars($input);
                    return $clean_data;
                }
                
                if (ctype_digit($_POST['id_num'])) {
                    
                    $stmt = $con -> prepare('SELECT * FROM tbl_users WHERE EMPLOYEE_NO=:emp_no');
                    $stmt -> bindValue(':emp_no', clean($_POST['id_num']));
                    $stmt -> execute();

                    $row = $stmt -> fetch(PDO::FETCH_ASSOC);

                    if ($row['EMPLOYEE_NO'] > 0) {

                        if (password_verify(clean($_POST['passwd']), $row['PASSWORD'])) {
                            if (ctype_digit($_POST['counter_num'])) {
                                if (strlen($_POST['counter_num']) < 3) {
                                    $stmt = $con -> prepare('UPDATE tbl_users SET COUNTER_NUM=:counter_num WHERE EMPLOYEE_NO=:emp_no');
                                    $stmt -> bindValue(':counter_num', clean($_POST['counter_num']));
                                    $stmt -> bindValue(':emp_no', clean($_POST['id_num']));
                                    $stmt -> execute();
                                    func::createRecord($con, $row['EMPLOYEE_NO']);
                                    header("Location: transactions.php");
                                }
                                else {
                                    $_SESSION['msg'] = '<div class="alert alert-danger alert-dismissible fade show mx-auto text-center my-4" role="alert" style="max-width: 800px;"><i class="fa fa-exclamation-triangle fa-lg me-2" aria-hidden="true"></i>Counter number should not be greater than 2 digits or more<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                    header("Location: login.php");
                                }
                            }
                            else {
                                $_SESSION['msg'] = '<div class="alert alert-danger alert-dismissible fade show mx-auto text-center my-4" role="alert" style="max-width: 800px;"><i class="fa fa-exclamation-triangle fa-lg me-2" aria-hidden="true"></i>Digits only for counter number<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                header("Location: login.php");
                            }
                        }
                        else {
                            $_SESSION['msg'] = '<div class="alert alert-danger alert-dismissible fade show mx-auto text-center my-4" role="alert" style="max-width: 800px;"><i class="fa fa-exclamation-triangle fa-lg me-2" aria-hidden="true"></i>Invalid username or password<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                            header("Location: login.php");
                        }

                    }
                    else {
                        $_SESSION['msg'] = '<div class="alert alert-danger alert-dismissible fade show mx-auto text-center my-4" role="alert" style="max-width: 800px;"><i class="fa fa-exclamation-triangle fa-lg me-2" aria-hidden="true"></i>Invalid username or password<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                        header("Location: login.php");
                    }

                }
                else {
                    $_SESSION['msg'] = '<div class="alert alert-danger alert-dismissible fade show mx-auto text-center my-4" role="alert" style="max-width: 800px;"><i class="fa fa-exclamation-triangle fa-lg me-2" aria-hidden="true"></i>ID Number should be numeric values only<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                    header("Location: login.php");
                }
            }
            else {
                header("Location: login.php");
            }
            unset($_SESSION['csrf_token']);
        }
    }
    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/snippets/endblock.php';