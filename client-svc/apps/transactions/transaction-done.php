<?php
    session_start();
    
    if (isset($_SESSION["queue_generated"])) {
        header("Location: gen-queue.php");
        die();
    }
    
    if (!isset($_SESSION["has_transaction"])) {
        header("Location: /client-svc/apps/index/client-type.php");
        die();
    }

    if (!isset($_SESSION["hdid"])) {
        header("Location: /client-svc/apps/index/health-declaration-form.php");
        die();
    }
    
    $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(16));

    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/snippets/block-content.php';
    require $_SERVER["DOCUMENT_ROOT"].'/client-svc/utils/connect_db.php';

    $stmt = $con -> prepare("SELECT * FROM tbl_transaction WHERE QUEUE_NUM=:queue_num AND STATUS=''");
    $stmt -> bindValue(":queue_num", $_SESSION["queue"]);
    $stmt -> execute();

    function url($data) {
        $temp = "";
        if ($data == "EMPLOYER") {
            $temp = "edit-emp-transaction.php";
        }
        else {
            $temp = "edit-member-transaction.php";
        }
        return $temp;
    }
    
?>
    <div class="text-center">
        <h5>Requested Transactions:</h5>
        <div class="row justify-content-center">
            <?php
                while ($data = $stmt -> fetch()) {
                    $transactions = explode(";", $data["TRANSACTION_DESC"]);
                    echo '
                    <div class="col-lg-4 mb-3">
                        <div class="card text-start h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-title">'.$data["PIN_PEN"].'</h6>
                                    <div>
                                        <a href="'.url($data["CLIENT_TYPE"]).'?id='.$data["ID"].'&queue='.$data["QUEUE_NUM"].'&csrf_token='.$_SESSION['csrf_token'].'">Change</a>
                                        <a href="#" class="text-danger ms-2" data-bs-toggle="modal" data-bs-target="#remove">Remove</a>
                                        <div class="modal fade" id="remove" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="removeLabel">Confirm Deletion?</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to remove this transaction?
                                                </div>
                                                <div class="modal-footer text-center">
                                                    <a class="btn btn-dark" href="remove-transaction.php?id='.$data["ID"].'&queue='.$data["QUEUE_NUM"].'&csrf_token='.$_SESSION['csrf_token'].'">Yes</a>
                                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
                                                </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <p class="card-text">'.$data["NAME"].'</p>
                                <h6 class="card-title">TRANSACTIONS</h6>';
                    foreach ($transactions as $key => $value) {
                        echo '<p class="card-text">'.$value.'</p>';
                    }
                    echo '
                            </div>
                        </div>
                    </div>';
                }
                echo '
                <div class="col-lg-4 mb-3">
                    <div class="card bg-light border-0 h-100" style="border: 2px dashed #6c757d !important;">
                        <div class="card-body text-center">
                            <a href="/client-svc/apps/index/client-type.php" class="stretched-link text-decoration-none">
                            Add Transaction
                            <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" fill="currentColor" class="bi bi-plus-circle d-block mx-auto mt-3" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                            </svg>
                            </a>
                        </div>
                    </div>
                </div>';
            ?>
        </div>
        <button type="button" class="btn btn-lg btn-dark mb-2" data-bs-toggle="modal" data-bs-target="#open">Open my queue number</button>
        <div class="modal fade" id="open" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Confirm?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Your status will be set as pending once you submit the transaction
                </div>
                <div class="modal-footer text-center">
                    <a href="gen-queue.php" class="btn btn-dark">Proceed</a>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Back</button>
                </div>
                </div>
            </div>
        </div>
        <div class="fst-italic text-muted small">You cannot add another transaction once you open your queue number.</div>
        </div>
    </div>
<?php
    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/snippets/endblock.php';