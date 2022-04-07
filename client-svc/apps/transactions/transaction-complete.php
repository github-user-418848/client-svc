<?php
    session_start();
    
    if (!isset($_SESSION["hdid"])) {
        header("Location: /client-svc/apps/index/health-declaration-form.php");
        die();
    }

    elseif (!isset($_SESSION["has_transaction"])) {
        header("Location: /client-svc/apps/index/client-type.php");
        die();
    }

    require $_SERVER["DOCUMENT_ROOT"].'/client-svc/utils/connect_db.php';
    
    $stmt = $con -> prepare("SELECT * FROM tbl_transaction WHERE QUEUE_NUM=:queue_num");
    $stmt -> bindValue(":queue_num", $_SESSION["queue"]);
    $stmt -> execute();

    $data = $stmt -> fetch();

    if ($data["STATUS"] == "DONE") {
        // setcookie('hdf_dat', '', time() - 1, '/', null, false, true);
        session_unset();
        
        $_SESSION['msg'] = '
        <div class="alert alert-info alert-dismissible fade show mx-auto text-center my-4" role="alert" style="max-width: 800px;">
            <i class="me-2 fa fa-info-circle fa-lg" aria-hidden="true"></i>
            Your transaction has been completed<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
        header("Location: /client-svc/apps/index/client-type.php");
    }
