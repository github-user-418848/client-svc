<?php
    session_start();

    if (!isset($_SESSION["hdid"])) {
        header("Location: /client-svc/apps/index/health-declaration-form.php");
        die();
    }

    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/snippets/block-content.php';

?>
<a href="/client-svc/apps/member/payment_service.php" class="text-decoration-none">
    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="40" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
    </svg>Back
</a>
<h2 class="text-center">Payment</h2>
    <form action="pin-handler.php" method="post" class="bg-white border p-4 mx-auto my-3" style="max-width: 500px;" id="form">
        <div class="text-center text-muted">Proceed to the contributions menu by entering your PIN</div>
        <div class="form-floating mt-2 mb-3">
            <input class="form-control" type="text" name="pin" id="pin" placeholder="">
            <label for="pin">PhilHealth ID Number</label>
            <div class="invalid-feedback" id="invalid-feedback-num"></div>
        </div>
        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo security::genCSRF();?>">
        <input type="submit" value="Enter PIN" class="btn btn-dark w-100 btn-lg" name="submit" id="submit">
    </form>
<script type="text/javascript" src="<?php echo STATIC_URL; ?>js/form-validation.js"></script>
<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/snippets/endblock.php';