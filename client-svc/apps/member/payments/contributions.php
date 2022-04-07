<?php
    
    session_start();
    
    if (!isset($_SESSION["hdid"])) {
        header("Location: /client-svc/apps/index/health-declaration-form.php");
        die();
    }

    if (!isset($_SESSION['id_num'])) {
        header("Location: pin.php");
        die();
    }

    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/snippets/block-content.php';
    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/connect_db.php';
    
    $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(16));
    
?>
<div class="text-center">
    <h2>Contributions</h2>
    <svg xmlns="http://www.w3.org/2000/svg" width="75" height="75" fill="currentColor" class="bi bi-three-dots text-muted d-block mx-auto" viewBox="0 0 16 16">
    <path d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z"/>
    </svg>
    <h3 class="display text-muted">No contributions yet</h3>
    <p class="text-muted">Apologies! This page isn't completely done yet :|</p>
    <a href="pin.php">Back</a>
</div>

<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/snippets/endblock.php';