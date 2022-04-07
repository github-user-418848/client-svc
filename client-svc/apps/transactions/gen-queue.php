<?php
    session_start();
    
    if (!isset($_SESSION["hdid"])) {
        header("Location: /client-svc/apps/index/health-declaration-form.php");
        die();
    }
    elseif (!isset($_SESSION["has_transaction"])) {
        header("Location: /client-svc/apps/index/client-type.php");
        die();
    }

    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/snippets/block-content.php';
    require $_SERVER["DOCUMENT_ROOT"].'/client-svc/utils/connect_db.php';
    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/query.php';
    
    $_SESSION["queue_generated"] = "1";
    query::pdoInsertQuery("UPDATE tbl_transaction SET STATUS='PENDING' WHERE QUEUE_NUM=:queue_num AND HDID=:hdid AND STATUS=''", array(":queue_num" => $_SESSION["queue"], ":hdid" => $_SESSION["hdid"]), $con);
    
?>
<div class="text-center">
    <svg xmlns="http://www.w3.org/2000/svg" width="95" height="95" fill="currentColor" class="bi bi-check-circle text-primary mb-3" viewBox="0 0 16 16">
        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
        <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
    </svg>
    <h5>Your queue number is: </h5>
    <h1 class="display-1"><?php echo $_SESSION["queue"]; ?></h1>
    <button type="button" class="btn btn-danger mb-3" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Cancel Transaction</button>
    <p class="text-muted mt-4">You are now in queue. Please proceed to the frontline area when your queue number is called.</p>
    <img class="img-fluid" src="<?php echo STATIC_URL; ?>svg/keep-distance.svg" alt="Could not load properly" width="200">
    <p class="text-muted mt-4"> Please observe social distancing at all times. Thank you!</p>

    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Cancel Transaction?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Do you want to cancel the transaction?
            </div>
            <div class="modal-footer text-center">
                <a href="cancel-transaction.php?queue=<?php echo $_SESSION["queue"]; ?>&hdid=<?php echo $_SESSION["hdid"]; ?>&csrf_token=<?php echo security::genCSRF(); ?>" class="btn btn-dark" id="cancel_btn">Yes</a>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
            </div>
            </div>
        </div>
    </div>
</div>
<?php

    if (isset($_SESSION["feedback"])) {
        echo '
        <form method="post" class="text-center">
            <p class="fst-italic text-muted small">Give us a feedback about on how the transaction went (Optional)</p>
                <div class="btn-group flex-wrap mb-3" role="group" aria-label="Basic radio toggle button group">
                    <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" value="Very Unsatisfied">
                    <label class="btn btn-outline-danger" for="btnradio1">
                        Very Unsatisfied
                    </label>

                    <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off" value="Unsatisfied">
                    <label class="btn btn-outline-warning" for="btnradio2">
                        Unsatisfied
                    </label>

                    <input type="radio" class="btn-check" name="btnradio" id="btnradio3" autocomplete="off" value="Satisfied">
                    <label class="btn btn-outline-info" for="btnradio3">
                        Satisfied
                    </label>

                    <input type="radio" class="btn-check" name="btnradio" id="btnradio4" autocomplete="off" value="Very Satisfied">
                    <label class="btn btn-outline-dark" for="btnradio4">
                        Very Satisfied
                    </label>
                    
                    <input type="radio" class="btn-check" name="btnradio" id="btnradio5" autocomplete="off" value="Excellent">
                    <label class="btn btn-outline-primary" for="btnradio5">
                        Excellent
                    </label>
                </div>
                <div class="mb-3">
                    <label for="comment" class="form-label fst-italic text-muted">Additional comment:</label>
                    <textarea class="form-control mx-auto" name="comment" id="comment" rows="3" style="max-width:500px;height:300px" maxlength="250" placeholder="Maximum characters: 250"></textarea>
                </div>
            <input type="submit" value="Submit Feedback" class="btn btn-dark mb-3" name="submit" id="submit">
        </form>';

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            if (input::sanitizeInput($_POST["submit"])) {
    
                $ratings = Array("VERY UNSATISFIED", "UNSATISFIED", "SATISFIED", "VERY SATISFIED", "EXCELLENT");
                $rating = input::validateChoices($_POST["btnradio"], $ratings);
    
                $temp = "";
                if (!empty($_POST["comment"])) {
                    $temp = input::sanitizeInput($_POST["comment"]);
                }
    
                $query = "INSERT INTO tbl_client_feedback (HDID, RATING, FEEDBACK) VALUES (:hdid, :rating, :feedback)";
                $values_arr = array(":hdid" => $_SESSION["hdid"], ":rating" => $rating, ":feedback" => $temp);
                query::pdoInsertQuery($query, $values_arr, $con);
                unset($_SESSION["feedback"]);
    
                $_SESSION['msg'] = '
                <div class="alert alert-success alert-dismissible fade show mx-auto text-center my-4" role="alert" style="max-width: 800px;">
                    <i class="fa fa-info-circle fa-lg me-2" aria-hidden="true"></i>
                        You\'re feedback has been sent successfully
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
                header("Location: gen-queue.php");
            }
        }
    }

    echo "
    <script src=\"/client-svc/static/js/jquery-3.6.0-min.js\"></script>
    <script nonce=\"".$_SESSION["random-nonce"]."\">
    (function(_0x986563,_0x154c79){var _0x295c9b=_0x986563();function _0x4d147c(_0x1b1a2b,_0x3bdf03){return _0x227d(_0x3bdf03- -0x37d,_0x1b1a2b);}while(!![]){try{var _0x46263b=parseInt(_0x4d147c(-0x24c,-0x24a))/0x1*(-parseInt(_0x4d147c(-0x23a,-0x23a))/0x2)+parseInt(_0x4d147c(-0x23a,-0x242))/0x3+parseInt(_0x4d147c(-0x234,-0x238))/0x4*(parseInt(_0x4d147c(-0x241,-0x240))/0x5)+parseInt(_0x4d147c(-0x253,-0x24b))/0x6*(-parseInt(_0x4d147c(-0x241,-0x241))/0x7)+parseInt(_0x4d147c(-0x23e,-0x23b))/0x8+parseInt(_0x4d147c(-0x240,-0x23d))/0x9*(-parseInt(_0x4d147c(-0x23c,-0x245))/0xa)+-parseInt(_0x4d147c(-0x24b,-0x243))/0xb*(-parseInt(_0x4d147c(-0x253,-0x249))/0xc);if(_0x46263b===_0x154c79)break;else _0x295c9b['push'](_0x295c9b['shift']());}catch(_0x66deb){_0x295c9b['push'](_0x295c9b['shift']());}}}(_0x5c9e,0xf229d),jQuery(function(_0xdfadd5){function _0x2f0d46(_0x572d22,_0x201551){return _0x227d(_0x572d22-0x10b,_0x201551);}var _0x1af37b=new WebSocket(_0x2f0d46(0x24a,0x247));_0x1af37b['onopen']=function(_0x451074){var _0x10f3eb={'hdid':_0x19da0a(0x11c,0x11c)};function _0x19da0a(_0x1fc16e,_0x49602e){return _0x2f0d46(_0x49602e- -0x126,_0x1fc16e);}_0x1af37b[_0x19da0a(0x118,0x11a)](JSON[_0x19da0a(0x126,0x126)](_0x10f3eb));},_0x1af37b[_0x2f0d46(0x24f,0x251)]=function(_0x3cd041){var _0x23e48c=JSON[_0x5e8964(0x2b0,0x2b5)](_0x3cd041['data']);function _0x5e8964(_0x4bfe1d,_0x562219){return _0x2f0d46(_0x4bfe1d-0x67,_0x562219);}_0x23e48c[_0x5e8964(0x2ab,0x2b0)]==_0x5e8964(0x2a9,0x2ad)&&(window[_0x5e8964(0x2a8,0x2a8)]='http://".$_SERVER["SERVER_ADDR"]."/client-svc/apps/transactions/transaction-complete.php');};}));function _0x227d(_0x2ccb21,_0x39307f){var _0x5c9e90=_0x5c9e();return _0x227d=function(_0x227d55,_0x2e159b){_0x227d55=_0x227d55-0x132;var _0x123efb=_0x5c9e90[_0x227d55];return _0x123efb;},_0x227d(_0x2ccb21,_0x39307f);}function _0x5c9e(){var _0x30e4c4=['stringify','2389104pYDcAc','2TKPASi','onmessage','4efXLXe','257988RfhewJ','253157qasCAA','79524aZZqfb','send','location','".$_SESSION["hdid"]."','2252990rIsmfg','hdid','2607vTznWO','1340736zxkSmL','287DpsQpm','7965115BxoOTf','parse','ws://".$_SERVER["SERVER_ADDR"].":1337','36XRcbVQ'];_0x5c9e=function(){return _0x30e4c4;};return _0x5c9e();}
    </script>";
    
    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/snippets/endblock.php';