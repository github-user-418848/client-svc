<?php
    session_start();
    
    if (isset($_SESSION["queue_generated"])) {
        header("Location: /client-svc/apps/member/services/gen-queue.php");
        die();
    }

    if (!isset($_SESSION["hdid"])) {
        header("Location: /client-svc/apps/index/health-declaration-form.php");
        die();
    }
    
    require $_SERVER["DOCUMENT_ROOT"].'/client-svc/snippets/block-content.php';

?>
<a href="/client-svc/apps/member/services/pin.php" class="text-decoration-none">
    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="40" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
    </svg>Back
</a>
<h2 class="text-center">PIN Lookup</h2>
<form action="pin-lookup-handler.php" method="post" class="bg-white border p-4 mx-auto my-3" style="max-width: 800px;">
    <div class="text-muted text-center">
        Make sure you've entered the exact information that you're registered with, to be able to lookup for your ID number if it exists
    </div>
    <div class="form-floating mt-2 mb-3">
        <div class="form-floating mb-3">
            <input class="form-control" type="text" name="last_name" id="last_name" placeholder="">
            <label for="last_name">Last Name</label>
        </div>
        <div class="form-floating mb-3">
            <input class="form-control" type="text" name="first_name" id="first_name" placeholder="">
            <label for="first_name">First Name</label>
        </div>
        <div class="form-floating mb-3">
            <input class="form-control" type="text" name="mid_name" id="mid_name" placeholder="">
            <label for="mid_name">Middle Name</label>
        </div>
        <div class="form-floating mb-3">
            <input class="form-control" type="date" name="birthdate" id="birthdate" placeholder="">
            <label for="birthdate">Birthdate</label>
        </div>
    </div>
    <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo security::genCSRF();?>">
    <input type="submit" value="Search" class="btn btn-dark w-100 btn-lg mb-3" name="submit" id="submit">
</form>
<?php
    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/snippets/endblock.php';