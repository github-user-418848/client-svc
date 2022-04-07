<?php
    session_start();
    
    if (isset($_SESSION["queue_generated"])) {
        header("Location: /client-svc/apps/employer/gen-queue.php");
        die();
    }
    
    if (!isset($_SESSION["has_transaction"])) {
        header("Location: /client-svc/apps/employer/ein.php");
        die();
    }

    if (!isset($_SESSION["hdid"])) {
        header("Location: /client-svc/apps/index/health-declaration-form.php");
        die();
    }
    
    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/snippets/block-content.php';
    require $_SERVER["DOCUMENT_ROOT"].'/client-svc/utils/connect_db.php';
    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/query.php';
  
    $transactions = "";
    if (security::checkMethod("GET")) {
        
        input::$redirect_location = "transaction-done.php";
        
        $fields = array("id", "queue", "csrf_token");

        input::checkFields($fields, $_GET);

        if (security::checkCSRF($_GET['csrf_token'])) {

            $id = input::checkNum($_GET["id"]);
            $queue = input::checkNum($_GET["queue"]);

            $_SESSION["edit_id"] = $id;
            $_SESSION["edit_queue"] = $queue;

            $data = query::pdoSelectQuery("
            SELECT *
            FROM tbl_transaction 
            WHERE ID=:id AND QUEUE_NUM=:queue_num", array(":id" => $id, "queue_num" => $queue), $con);
            $transactions = explode(";", $data["TRANSACTION_DESC"]);

            $_SESSION["member_type"] = $data["TRANSACTION_ID"];
        }
    }
?>
<h2 class="text-center">Edit Transaction</h2>
<form action="edit-emp-transaction-handler.php" method="post" class="bg-white border p-4" id="form">
    <div class="row">
        <div class="col-lg-6">
            <h3>Information</h3>
            <?php 
                function checkEmptyData($data) {
                    $temp = "EMPTY";
                    if (!empty($data)) {
                        $temp = $data;
                    }
                    return $temp;
                }
                function checkArrayMatches($data, $haystack) {
                    $temp = "";
                    if (in_array($data, $haystack)) {
                        $temp = "checked";
                    }
                    return $temp;
                }
                function checkMatches($data, $data2) {
                    $temp = "";
                    if ($data == $data2) {
                        $temp = "checked";
                    }
                    return $temp;
                }

                function selectMatches($data, $data2) {
                    $temp = "";
                    if ($data == $data2) {
                        $temp = "selected";
                    }
                    return $temp;
                }

                echo '
                <hr>
                <h5 class="mb-1">Employer</h5>
                <p class="mb-1">'.$data["PIN_PEN"].'</p>
                <hr>
                <h5 class="mb-1">Full Name</h5>
                <p class="mb-1">'.$data["NAME"].'</p>
                <hr>
                <h5 class="mb-1">Address</h5>
                <p class="mb-1">'.checkEmptyData($data["ADDRESS"]).'</p>
                <hr>
                ';
            
                if ($_SESSION["member_type"] == "NEW_EMPLOYER") {
                    echo '
                    <h5 class="mb-1">Contact Number</h5>
                    <p class="mb-1">'.$data["CONTACT_NUMBER"].'</p>
                    <hr>
                    <h5 class="mb-1">Email Address</h5>
                    <p class="mb-1">'.checkEmptyData($data["EMAIL_ADDRESS"]).'</p>
                    <hr>
                    </div>
                    <div class="col-lg-6">
                    <h3>Choose Transaction</h3>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Member Data Record" name="transaction[]" id="transaction1" '.checkArrayMatches("MEMBER DATA RECORD", $transactions).'>
                        <label for="transaction" class="form-check-label">Member Data Record</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="PNC/ID Card" name="transaction[]" id="transaction2" '.checkArrayMatches("PNC/ID CARD", $transactions).'>
                        <label for="transaction" class="form-check-label">PNC/ID Card</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Members Updating" name="transaction[]" id="transaction5" '.checkArrayMatches("MEMBERS UPDATING", $transactions).'>
                        <label for="transaction" class="form-check-label">Member Updating</label>
                        <div class="invalid-feedback">
                            Please select at least one of the choices
                        </div>
                    </div>
                    <div class="form-floating mb-3" style="max-width: 190px">
                        <input type="text" class="form-control" name="emp_num" id="emp_num" value="'.$data["MEMBER_UPDATE_COUNT"].'">
                        <label for="emp_num" class="form-control-label">No. of Employees</label>
                        <div class="invalid-feedback" id="invalid-feedback-num-2"></div>
                    </div>
                    <h3>Purpose of Request</h3>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="request" id="request" checked>
                        <label for="request" class="form-check-label">For Registration</label>
                    </div>
                    <h3>Authorization</h3>
                    This is to authorize the current user to secure the requested document
                    <div class="form-floating mb-3">
                        <select class="form-select mb-3" name="gov_id" id="gov_id">
                            <option value="Philhealth ID" '.selectMatches($data["ID_PRESENTED"], "PHILHEALTH ID").'>PhilHealth ID</option>
                            <option value="Philippine Passport" '.selectMatches($data["ID_PRESENTED"], "PHILIPPINE PASSPORT").'>Philippine Passport</option>
                            <option value="Driver&#039;s License" '.selectMatches($data["ID_PRESENTED"], "DRIVER'S LICENSE").'>Driver&#039;s License</option>
                            <option value="SSS UMID Card" '.selectMatches($data["ID_PRESENTED"], "SSS UMID CARD").'>SSS UMID Card</option>
                            <option value="TIN Card" '.selectMatches($data["ID_PRESENTED"], "TIN CARD").'>TIN Card</option>
                            <option value="Postal ID" '.selectMatches($data["ID_PRESENTED"], "POSTAL ID").'>Postal ID</option>
                            <option value="Voter&#039;s ID" '.selectMatches($data["ID_PRESENTED"], "VOTER'S ID").'>Voter&#039;s ID</option>
                            <option value="Professional Regulation Commission ID" '.selectMatches($data["ID_PRESENTED"], "PROFESSIONAL REGULATION COMMISSION ID").'>Professional Regulation Commission ID</option>
                            <option value="Senior Citizen ID" '.selectMatches($data["ID_PRESENTED"], "SENIOR CITIZEN ID").'>Senior Citizen ID</option>
                            <option value="O.F.W ID" '.selectMatches($data["ID_PRESENTED"], "O.F.W ID").'>O.F.W ID</option>
                        </select>
                        <label for="email">ID Presented</label>
                    </div>
                    ';
                }
                else {
                    echo '
                    </div>
                    <div class="col-lg-6">
                    <h3>Transaction Type</h3>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Member Data Record" name="transaction[]" id="transaction1" '.checkArrayMatches("MEMBER DATA RECORD", $transactions).'>
                        <label for="transaction1" class="form-check-label">Member Data Record</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="PNC/ID Card" name="transaction[]" id="transaction2" '.checkArrayMatches("PNC/ID CARD", $transactions).'>
                        <label for="transaction2" class="form-check-label">PNC/ID Card</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Request Certificate for Business Permit" name="transaction[]" id="transaction3" '.checkArrayMatches("REQUEST CERTIFICATE FOR BUSINESS PERMIT", $transactions).'>
                        <label for="transaction3" class="form-check-label">Request Certificate for Business Permit</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Record Updating" name="transaction[]" id="transaction4" '.checkArrayMatches("RECORD UPDATING", $transactions).'>
                        <label for="transaction4" class="form-check-label">Record Updating</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Members Updating" name="transaction[]" id="transaction5" '.checkArrayMatches("MEMBERS UPDATING", $transactions).'>
                        <label for="transaction5" class="form-check-label">Members Updating</label>
                        <div class="invalid-feedback">
                            Please select at least one of the choices
                        </div>
                    </div>
                    <div class="form-floating mb-3" style="max-width: 190px">
                        <input type="text" class="form-control" name="emp_num" id="emp_num" value="'.$data["MEMBER_UPDATE_COUNT"].'">
                        <label for="emp_num" class="form-control-label">No. of Employees</label>
                        <div class="invalid-feedback" id="invalid-feedback-num-2"></div>
                    </div>
                    <h3>Purpose of Request</h3>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" value="For Hospitalization" name="request" id="request" '.checkMatches("FOR HOSPITALIZATION", $data["PURPOSE"]).'>
                        <label for="request" class="form-check-label">For Hospitalization</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" value="For File" name="request" id="request" '.checkMatches("FOR FILE", $data["PURPOSE"]).'>
                        <label for="request" class="form-check-label">For File</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" value="For Replacement" name="request" id="request" '.checkMatches("FOR REPLACEMENT", $data["PURPOSE"]).'>
                        <label for="request" class="form-check-label">For Replacement</label>
                        <div class="invalid-feedback">
                            Please select at least one of the choices
                        </div>
                    </div>';
                }
            ?>
            Signed:<br>
            <img class="img-fluid mb-2" src="<?php echo $data["ESIGNATURE"]?>" alt="Missing signature" width="500">
        </div>
    </div>
    <div class="text-center">
    <a href="transaction-done.php" class="btn btn-danger btn-lg mb-3">Cancel</a>
    <input type="submit" value="Save" class="btn btn-lg btn-dark mb-3" name="save" id="save">
    </div>
</form>
<script src="<?php echo STATIC_URL; ?>js/form-validation.js"></script>
<?php
    require_once $_SERVER["DOCUMENT_ROOT"]."/client-svc/snippets/endblock.php";