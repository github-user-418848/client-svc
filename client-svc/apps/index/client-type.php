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

    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/snippets/block-content.php';

?>
<div class="text-center">
    <svg xmlns="http://www.w3.org/2000/svg" width="125" height="125" fill="currentColor" class="bi bi-check-circle text-success" viewBox="0 0 16 16">
        <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
    </svg>
    <h3 class="mt-3">I have filled out my Health Declaration Form</h3>
    <h6><?php echo $_SESSION["requestor_name"]; ?></h6>
    <div class="fst-italic small text-muted mb-3">Select your client type for the transaction</div>
    <ol class="list-group list-group mb-2">
        <li class="list-group-item p-3">
            <a class="card-link" href="/client-svc/apps/member/payment_service.php">Member</a>
        </li>
        <li class="list-group-item p-3">
            <a class="card-link" href="/client-svc/apps/employer/ein.php">Employer</a>
        </li>
        <?php 
        if (isset($_SESSION["has_transaction"])) {       
            echo '
            <li class="list-group-item p-3">
            <a class="card-link" href="/client-svc/apps/transactions/transaction-done.php" class="d-flex justify-content-center mb-2">
                My Transactions
            </a>
            </li>';
        }?>
    </ol>


</div>
<?php

    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/snippets/endblock.php';