<?php
    session_start();
    
    if (isset($_SESSION["queue_generated"])) {
        header("Location: /client-svc/apps/transactions/gen-queue.php");
        die();
    }
    
    if (!isset($_SESSION["hdid"])) {
        header("Location: /client-svc/apps/index/health-declaration-form.php");
        die();
    }

    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/snippets/block-content.php';
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(16));
    }
?>
<a href="ein.php" class="text-decoration-none">
    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="40" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
    </svg>Back
</a>
<h2 class="text-center">PEN Lookup</h2>
<form method="post" class="mx-auto py-3 bg-white border px-4 my-3">
    <div class="text-muted text-center">
        Enter the Employer Name or the ID number
    </div>
    <div class="form-floating mt-2 mb-3">
        <div class="form-floating mb-3">
            <input class="form-control" type="text" name="q" id="q" placeholder="">
            <label for="last_name">Search for ID/Name</label>
        </div>
    </div>
    <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <input type="submit" value="Search" class="btn btn-dark w-100 btn-lg mb-3" name="search" id="search">
</form>

<?php

    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/connect_db.php';
    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/query.php';

    if (isset($_POST["search"])) {

        if (security::checkMethod("POST")) {

            input::$redirect_location = "ein-lookup.php";

            $fields = array("q", "csrf_token");

            input::checkFields($fields, $_POST);

            if (security::checkCSRF($_POST['csrf_token'])) {
                
                $search = input::sanitizeInput($_POST["q"]);

                $stmt = $con -> prepare("SELECT * FROM tbl_employers WHERE EMPID_NO LIKE :id OR EMP_NAME LIKE :name LIMIT 20");
                $stmt -> bindValue(":id", "%".$search."%");
                $stmt -> bindValue(":name", "%".$search."%");
                $stmt -> execute();
                
                if ($stmt -> rowCount() < 1) {
                    $_SESSION['msg'] = "
                    <div class=\"alert alert-danger alert-dismissible fade show mx-auto text-center my-4\" role=\"alert\" style=\"max-width: 800px;\">
                        <i class=\"fa fa-exclamation-triangle fa-lg me-2\" aria-hidden=\"true\"></i>
                        Sorry, we couldn&#39;t find anything with the information you&#39;ve provided
                        <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>
                    </div>";
                    header("Location: ein-lookup.php");
                }
                while ($data = $stmt -> fetch()) {
                    echo '
                    <div class="card d-flex p-3" id="card_'.$data['EMPID_NO'].'">
                    <div class="pb-3 mb-0 lh-sm w-100">
                        <div class="d-flex justify-content-between">
                            <h5>'.$data["EMPID_NO"].'</h5>
                            <a class="stretched-link" href="set-ein.php?ein='.$data['EMPID_NO'].'&csrf_token='.$_SESSION['csrf_token'].'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-box-arrow-in-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0v-2z"/>
                                <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                            </svg>
                            </a>
                        </div>
                        <h6>'.$data["EMP_NAME"].'</h6>
                        <div class="text-muted small">'.$data["TEL_NO"].'</div>
                    </div>
                </div>';
                }
            }
        }
    }

    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/snippets/endblock.php';