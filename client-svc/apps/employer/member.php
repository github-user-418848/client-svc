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
    
    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/snippets/block-content.php';
    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/connect_db.php';
    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/query.php';
    
    $query = "SELECT * FROM tbl_employers WHERE EMPID_NO=:id LIMIT 1";
    $values_arr = array(":id" => $_SESSION["id_num"]);
    $data = query::pdoSelectQuery($query, $values_arr, $con);
    
?>
<h2 class="text-center">Employer Transaction Form</h2>
<form action="member-handler.php" method="post" id="form">
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="bg-white border p-3 mb-2">
                <h3 class="mt-2">Info</h3>
                <hr>
                <h5 class="mb-1">Employer ID</h5>
                <p class="mb-1"><?php if ($data["EMPID_NO"]) echo $data["EMPID_NO"]; else echo "NULL"; ?></p>
                <hr>
                <h5 class="mb-1">Employer Name</h5>
                <p class="mb-1"><?php if ($data["EMP_NAME"]) echo $data["EMP_NAME"]; else echo "NULL"; ?></p>
                <hr>
                <h5 class="mb-1">Employer Address</h5>
                <p class="mb-1"><?php if ($data["ADDRESS"]) echo $data["ADDRESS"]; else echo "NULL"; ?></p>
                <hr>
                <h5 class="mb-1">Tel. No.</h5>
                <p class="mb-1"><?php if ($data["TEL_NO"]) echo $data["TEL_NO"]; else echo "NULL"; ?></p>
                <hr>
                <h5 class="mb-1">Email</h5>
                <p class="mb-1"><?php if ($data["EMAIL"]) echo $data["EMAIL"]; else echo "NULL"; ?></p>
                <hr>
                <h5 class="mb-1">Status</h5>
                <p class="mb-1"><?php 
                    if ($data["STATUS"]) {
                        switch ($data["STATUS"]) {
                            case "T": echo "
                            <span class='badge rounded-pill bg-warning'>
                            TEMPORARILY CLOSED
                            </span>"; break;
                            case "O": echo "
                            <span class='badge rounded-pill bg-success'>
                            OPEN
                            </span>"; break;
                            case "C": echo "
                            <span class='badge rounded-pill bg-danger'>
                            CLOSED
                            </span>"; break;
                        }
                    }
                    else echo "NULL"; ?>
                </p>
                <hr>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="bg-white border p-3 mb-2">
                <div class="row mt-3 mx-auto">
                    <div class="col">
                    <h3>Transaction Type</h3>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="Member Data Record" name="transaction[]" id="transaction1">
                            <label for="transaction1" class="form-check-label">Member Data Record</label>
                        </div>
                        <div class="input-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="PNC/ID Card" name="transaction[]" id="transaction2">
                                <label for="transaction2" class="form-check-label">PNC / ID</label>
                            </div>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="Request Certificate for Business Permit" name="transaction[]" id="transaction3">
                            <label for="transaction3" class="form-check-label">Request Cert. for Business Permit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="Record Updating" name="transaction[]" id="transaction4">
                            <label for="transaction4" class="form-check-label">Record Updating</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="Members Updating" name="transaction[]" id="transaction5">
                            <label for="transaction5" class="form-check-label">Members Updating</label>
                            <div class="invalid-feedback">
                                Please select at least one of the choices
                            </div>
                            <div class="form-floating mb-3" style="max-width: 190px">
                                <input type="text" class="form-control" name="emp_num" id="emp_num">
                                <label for="emp_num" class="form-control-label">No. of Employees</label>
                                <div class="invalid-feedback" id="invalid-feedback-num-2"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <h3>Purpose of Request</h3>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" value="For Hospitalization" name="request" id="request">
                            <label for="request" class="form-check-label">For Hospitalization</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" value="For File" name="request" id="request">
                            <label for="request" class="form-check-label">For File</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" value="For Replacement" name="request" id="request">
                            <label for="request" class="form-check-label">For Replacement</label>
                            <div class="invalid-feedback">
                                Please select at least one of the choices
                            </div>
                        </div>
                    </div>
                </div>
                <div id="signature-pad" class="signature-pad mx-auto mt-2">
                    <div class="mb-1">
                        <button type="button" class="btn btn-outline-danger" data-action="clear">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-eraser" viewBox="0 0 16 16">
                                <path d="M8.086 2.207a2 2 0 0 1 2.828 0l3.879 3.879a2 2 0 0 1 0 2.828l-5.5 5.5A2 2 0 0 1 7.879 15H5.12a2 2 0 0 1-1.414-.586l-2.5-2.5a2 2 0 0 1 0-2.828l6.879-6.879zm2.121.707a1 1 0 0 0-1.414 0L4.16 7.547l5.293 5.293 4.633-4.633a1 1 0 0 0 0-1.414l-3.879-3.879zM8.746 13.547 3.453 8.254 1.914 9.793a1 1 0 0 0 0 1.414l2.5 2.5a1 1 0 0 0 .707.293H7.88a1 1 0 0 0 .707-.293l.16-.16z"/>
                            </svg>&nbsp;Clear Signature
                        </button>
                    </div>
                    <div class="signature-pad--body">
                        <canvas id="signature" class="border border-1 rounded-3"></canvas>
                    </div>
                    <div class="small text-danger text-center visually-hidden" id="invalid-img-data">
                        Please add a signature above
                    </div>
                    <div class="text-center">Sign above</div>
                </div>
                <input type="hidden" name="img-data" id="img-data">
                <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo security::genCSRF();?>">
                <div class="text-center">
                    <a href="ein.php" class="btn btn-danger btn-lg mx-auto">Cancel</a>
                    <input type="submit" value="Submit" class="btn btn-dark btn-lg mx-auto" name="submit" id="submit">
                </div>
            </div>
        </div>
    </div>
</form>
<script src="<?php echo STATIC_URL; ?>js/form-validation.js"></script>
<script src="<?php echo STATIC_URL; ?>js/signature_pad.umd.js"></script>
<script src="<?php echo STATIC_URL; ?>js/app.js"></script>
<?php
    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/snippets/endblock.php';