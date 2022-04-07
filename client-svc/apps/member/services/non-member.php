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

    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/snippets/block-content.php';
    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/query.php';

?>
<h2 class="text-center">New Member</h2>
<form action="non-member-handler.php" method="post" class="bg-white border p-4" id="form">
    <div class="row">
        <div class="col-lg-6">
            <h3>Information</h3>
            <div class="form-floating mb-3">
                <input class="form-control" type="text" name="last_name" id="last_name" placeholder="" >
                <label for="last_name">Last Name</label>
                <div class="invalid-feedback">
                    This field is required.
                </div>
            </div>
            <div class="form-floating mb-3">
                <input class="form-control" type="text" name="first_name" id="first_name" placeholder="" >
                <label for="first_name">First Name</label>
                <div class="invalid-feedback">
                    This field is required.
                </div>
            </div>
            <div class="form-floating mb-3">
                <input class="form-control" type="text" name="mid_name" id="mid_name" placeholder="" >
                <label for="mid_name">Middle Name</label>
                <div class="invalid-feedback">
                    This field is required.
                </div>
            </div>
            <div class="form-floating mb-3">
                <input class="form-control" type="text" name="addr" id="addr" placeholder="" >
                <label for="addr">Address of Member</label>
                <div class="invalid-feedback">
                    This field is required.
                </div>
            </div>
            <div class="form-floating mb-3">
                <input class="form-control" type="text" name="contact" id="contact" placeholder="" >
                <label for="contact">Contact Number</label>
                <div class="invalid-feedback" id="invalid-feedback-num">
                    This field is required.
                </div>
            </div>
            <div class="form-floating mb-3">
                <input class="form-control" type="email" name="email" id="email" placeholder="" >
                <label for="email">Email Address (Optional)</label>
                <div class="invalid-feedback" id="invalid-feedback-email"></div>
            </div>
        </div>
        <div class="col-lg-6">
            <h3>Choose Transaction</h3>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="Member Data Record" name="transaction[]" id="transaction1">
                <label for="transaction[]" class="form-check-label">Member Data Record</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="PNC/ID Card" name="transaction[]" id="transaction2">
                <label for="transaction[]" class="form-check-label">PNC/ID Card</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="Member Updating" name="transaction[]" id="transaction3">
                <label for="transaction[]" class="form-check-label">Member Updating</label>
                <div class="invalid-feedback">Please select at least one of the choices</div>
            </div>
            <h3>Purpose of Request</h3>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="request" id="request" checked >
                <label for="request" class="form-check-label">For Registration</label>
            </div>
            <h3>Authorization</h3>
            This is to authorize <strong><?php echo $_SESSION["requestor_name"]; ?></strong> to secure the requested document
            <div class="form-floating mb-3">
                <select class="form-select mb-3" name="gov_id" id="gov_id" aria-label="Default select example">
                    <option value="Philippine Passport" selected>Philippine Passport</option>
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
                <div class="text-center mb-3">Sign above</div>
            </div>
            <input type="hidden" name="img-data" id="img-data">
            <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo security::genCSRF();?>">
        </div>
    </div>
    <div class="text-center">
    <a href="pin.php" class="btn btn-danger btn-lg">Cancel</a>
    <input type="submit" value="Submit" class="btn btn-lg btn-dark" name="submit" id="submit">
    </div>
</form>
<script type="text/javascript" src="<?php echo STATIC_URL; ?>js/form-validation.js"></script>
<script type="text/javascript" src="<?php echo STATIC_URL; ?>js/signature_pad.umd.js"></script>
<script type="text/javascript" src="<?php echo STATIC_URL; ?>js/app.js"></script>
<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/snippets/endblock.php';