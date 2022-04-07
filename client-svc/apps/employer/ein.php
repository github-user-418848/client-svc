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
    
    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/snippets/block-content.php';
    
    if (isset($_SESSION["notify"])) {
        echo '
            <div class="modal fade" id="notify" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Request for an assistance</h5>
                        </div>
                        <div class="modal-body">
                        Please kindly approach the security guard for assistance. Thank you!
                        </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Got It!</button>
                        </div>
                    </div>
                </div>
            </div>
            <script nonce="'.$_SESSION["random-nonce"].'">
                var myModal = new bootstrap.Modal(document.getElementById(\'notify\'));
                myModal.show();
            </script>';
        unset($_SESSION["notify"]);
    }
?>
<a href="/client-svc/apps/index/client-type.php" class="text-decoration-none">
    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="40" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
    </svg>Back
</a>
<h2 class="text-center">Employer</h2>
    <form action="ein-handler.php" method="post" class="bg-white border p-4 mx-auto my-3" style="max-width: 500px;" id="form">
        <div class="text-center text-muted">Proceed to the transaction form by entering your ID</div>
        <div class="form-floating mt-2 mb-3">
            <input class="form-control" type="text" name="ein" id="ein" placeholder="">
            <label for="ein">Employer ID Number</label>
            <div class="invalid-feedback fw-bold" id="invalid-feedback-num"></div>
        </div>
        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo security::genCSRF();?>">
        <input type="submit" value="Existing Employer" class="btn btn-dark w-100 btn-lg mb-2" name="submit" id="submit">
        <div class="text-center text-muted">or</div>
        <a href="non-member.php" class="btn btn-success w-100 btn-lg mt-2 mb-3">Registration</a>
        <a href="ein-lookup.php" class="d-flex justify-content-center">Employer PEN Search</a>
    </form>
<script type="text/javascript" src="<?php echo STATIC_URL; ?>js/form-validation.js"></script>
<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/snippets/endblock.php';