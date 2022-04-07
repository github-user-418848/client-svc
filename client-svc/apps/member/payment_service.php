<?php
    session_start();

    if (isset($_SESSION["queue_generated_emp"])) {
        header("Location: /client-svc/apps/employer/gen-queue.php");
        die();
    }
    
    if (isset($_SESSION["queue_generated"])) {
        header("Location: /client-svc/apps/member/services/gen-queue.php");
        $_SESSION["msg"] = "";
        die();
    }

    if (!isset($_SESSION["hdid"])) {
        header("Location: /client-svc/apps/index/health-declaration-form.php");
        die();
    }

    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/snippets/block-content.php';

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
        </script>
        ';
        unset($_SESSION["notify"]);
    }
?>
<a href="/client-svc/apps/index/client-type.php" class="text-decoration-none">
    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="40" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
    </svg>Back
</a>
<h2 class="text-center mt-3">Choose Payment/Service</h2>
<div class="row row-cols-1 row-cols-lg-2 align-items-stretch g-4 py-5">
    <div class="col text-center">
        <a href="/client-svc/apps/member/payments/pin.php"><img src="<?php echo STATIC_URL?>img/payment.jpg" class="custom img-fluid" alt="Payment" width="245"></a>
    </div>
    <div class="col text-center">
        <a href="/client-svc/apps/member/services/pin.php"><img src="<?php echo STATIC_URL?>img/services.jpg" class="custom img-fluid" alt="Service" width="245"></a>  
    </div>
</div>
<?php
    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/snippets/endblock.php';