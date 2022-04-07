<?php
    session_start();
    
    if (isset($_SESSION["queue_generated"])) {
        $_SESSION['msg'] = "
        <div class=\"alert alert-info alert-dismissible fade show mx-auto text-center my-4\" role=\"alert\" style=\"max-width: 800px;\">
            <i class=\"fa fa-exclamation-triangle fa-lg me-2\" aria-hidden=\"true\"></i>
            You can't add another transaction once you have your queue number. Inquire at the frontline area if you have any additional transactions.
            <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>
        </div>";
        header("Location: /client-svc/apps/member/services/gen-queue.php");
        die();
    }

    if (!isset($_SESSION["hdid"])) {
        header("Location: /client-svc/apps/index/health-declaration-form.php");
        die();
    }

    if (!isset($_SESSION['id_num'])) {
        header("Location: pin.php");
        die();
    }

    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/snippets/block-content.php';
    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/connect_db.php';
    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/query.php';

    $query = "SELECT * FROM tbl_members WHERE MEMID_NO=:id LIMIT 1";
    $values_arr = array(":id" => $_SESSION["id_num"]);
    $data = query::pdoSelectQuery($query, $values_arr, $con);
    
    function marital_status($status) {
        switch ($status) {
            case 'S':
            echo "Single";
            break;
            case 'M':
            echo "Married";
            break;
            case 'W':
            echo "Widowed";
            break;
        }
    }
?>
<h2 class="text-center">Member</h2>
<form action="member-handler.php" method="post" id="form">
    <div class="row">
        <div class="col-lg-5 mb-2">
            <div class="bg-white border h-100 p-3">
                <h3 class="mt-2">Info</h3>
                <hr>
                <h5 class="mb-1">Philhealth ID</h5>
                <p class="mb-1"><?php echo $_SESSION["id_num"]; ?></p>
                <hr>
                <h5 class="mb-1">Last Name</h5>
                <p class="mb-1"><?php if ($data["LASTNAME"]) echo $data["LASTNAME"]; else echo "EMPTY"; ?></p>
                <hr>
                <h5 class="mb-1">First Name</h5>
                <p class="mb-1"><?php if ($data["FIRSTNAME"]) echo $data["FIRSTNAME"]; else echo "EMPTY"; ?></p>
                <hr>
                <h5 class="mb-1">Middle Name</h5>
                <p class="mb-1"><?php if ($data["MIDDLENAME"]) echo $data["MIDDLENAME"]; else echo "EMPTY"; ?></p>
                <hr>
                <h5 class="mb-1">Address</h5>
                <p class="mb-1"><?php if ($data["ADDRESS"]) echo $data["ADDRESS"]; else echo "EMPTY"; ?></p>
                <hr>
                <h5 class="mb-1">Marital Status</h5>
                <p class="mb-1"><?php if ($data["MARITAL_STATUS"]) marital_status($data["MARITAL_STATUS"]); else echo "EMPTY"; ?></p>
                <hr>
            </div>
        </div>
        <div class="col-lg-7 mb-2">
            <div class="bg-white border p-3">
                <div class="row mt-3 mx-auto">
                    <div class="col stretch">
                        <h3>Transaction Type</h3>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="Member Data Record" name="transaction[]" id="transaction[]">
                            <label for="transaction" class="form-check-label">Member Data Record</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="PNC/ID Card" name="transaction[]" id="transaction[]">
                            <label for="transaction" class="form-check-label">PNC/ID Card</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="Premium Contribution" name="transaction[]" id="transaction[]">
                            <label for="transaction" class="form-check-label">Premium Contribution</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="Member Updating" name="transaction[]" id="transaction[]">
                            <label for="transaction" class="form-check-label">Member Updating</label>
                            <div class="invalid-feedback">Please select at least one of the choices</div>
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
                            <div class="invalid-feedback">Please select at least one of the choices</div>
                        </div>
                    </div>
                </div>
            <h3>Authorization</h3>
            <div class="mb-3">
                This is to authorize <b><?php echo htmlspecialchars($data["LASTNAME"].", ".$data["FIRSTNAME"]." ".$data["MIDDLENAME"])?></b> to secure the requested document
            </div>
            <div class="form-floating">
                <select class="form-select" name="gov_id" id="gov_id" aria-label="Government ID" required>
                    <option value="PhilHealth ID" selected>PhilHealth ID</option>
                    <option value="Philippine Passport">Philippine Passport</option>
                    <option value="Driver's License">Driver's License</option>
                    <option value="SSS UMID Card">SSS UMID Card</option>
                    <option value="TIN Card">TIN Card</option>
                    <option value="Postal ID">Postal ID</option>
                    <option value="Voter's ID">Voter's ID</option>
                    <option value="Professional Regulation Commission ID">Professional Regulation Commission ID</option>
                    <option value="Senior Citizen ID">Senior Citizen ID</option>
                    <option value="O.F.W ID">O.F.W ID</option>
                </select>
                <label for="email">ID Presented</label>
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
                <div class="small text-danger text-center visually-hidden" id="invalid-img-data">Please add a signature above</div>
                <div class="text-center mb-2">Sign above</div>
            </div>
            <input type="hidden" name="img-data" id="img-data">
            <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo security::genCSRF();?>">
            <div class="text-center">
                <a href="pin.php" class="btn btn-danger btn-lg mx-auto">Cancel</a>
                <input type="submit" value="Submit" class="btn btn-dark btn-lg mx-auto" name="submit" id="submit">
            </div>
        </div>
    </div>
</form>
<script type="text/javascript" src="<?php echo STATIC_URL; ?>js/form-validation.js"></script>
<script src="<?php echo STATIC_URL; ?>js/signature_pad.umd.js"></script>
<script src="<?php echo STATIC_URL; ?>js/app.js"></script>
<?php
    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/snippets/endblock.php';