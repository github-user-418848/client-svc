<?php
    session_start();

    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/connect_db.php';
    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/query.php';
    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/utils/chk_login.php';

    if (!func::checkLoginState($con)) {
        header("Location: login.php");
        die();
    }
    
    function clean ($input) {
        $clean_data = trim($input);
        $clean_data = stripslashes($input);
        $clean_data = htmlspecialchars($input);
        return $clean_data;
    }

    function split($data) {
        $temp = "";
        foreach ($items = explode(";", $data) as $value) {
            $temp .= $value."<br>";
        }
        return $temp;
    }

    function checkIfEmpty($data) {
        $temp = "EMPTY";
        if (!empty($data)) {
            $temp = $data;
        }
        return $temp;
    }

    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/snippets/block-content.php';

    input::$redirect_location = "transactions.php";


    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $id = input::checkNum($_GET["id"]);
        $data = query::pdoSelectQuery("SELECT * FROM tbl_transaction WHERE ID=:id LIMIT 1", array(':id' => $id), $con);
        echo '
        <div class="alert alert-warning text-center mx-auto visually-hidden" style="width: 390px" id="msg"></div>
        <a href="client-details.php?hdid='.$data["HDID"].'&queue='.$data["QUEUE_NUM"].'" class="text-decoration-none">
            <svg xmlns="http://www.w3.org/2000/svg" width="35" height="40" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
            </svg>Back
        </a>
        <div class="bg-white border p-3 my-3">
            <h2 class="text-center">Transaction Form #'.$data["ID"].'</h2><hr>
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <h5 class="mt-3">Queue Number</h5>
                    '.$data["QUEUE_NUM"].'
                    <h5 class="mt-3">Transaction ID</h5>
                    '.$data["TRANSACTION_ID"].'
                    <h5 class="mt-3">ID Number</h5>
                    '.$data["PIN_PEN"].'
                    <h5 class="mt-3">Full Name</h5>
                    '.$data["NAME"].'
                    <h5 class="mt-3">Contact Number</h5>
                    '.checkIfEmpty($data["CONTACT_NUMBER"]).'
                    <h5 class="mt-3">Email Address</h5>
                    '.checkIfEmpty($data["EMAIL_ADDRESS"]).'
                    <h5 class="mt-3">Purpose</h5>
                    '.$data["PURPOSE"].'
                </div>
                <div class="col-lg-6">
                    <h5 class="mt-3">Address</h5>
                    '.checkIfEmpty($data["ADDRESS"]).'
                    <h5 class="mt-3">Requestor Name</h5>
                    '.$data["REQUESTOR_NAME"].'
                    <h5 class="mt-3">Priority</h5>
                    '.$data["PRIORITY"].'
                    <h5 class="mt-3">ID Presented</h5>
                    '.checkIfEmpty($data["ID_PRESENTED"]).'
                    <h5 class="mt-3">Status</h5>
                    '.$data["STATUS"].'
                    <h5 class="mt-3">Signature</h5>
                    <p class="mb-1 text-center"><img class="border img-fluid" src="'.clean($data["ESIGNATURE"]).'" width="350" alt="Coudn&lsquo;t Load Properly">
                </div>
            </div>
            <div class="mx-auto">
                <h5 class="mt-3">Transactions</h5>
                '.split($data["TRANSACTION_DESC"]).'
                NUMBER OF MEMBER(S) - '.$data["MEMBER_UPDATE_COUNT"].'
            </div>
            <button type="button" class="btn btn-dark btn-lg w-100 mt-3" data-bs-toggle="modal" data-bs-target="#add_transaction">Add Transaction</button>
            <div class="modal fade" id="add_transaction" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="add_transaction_label" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="client-transaction.php?hdid='.$data["HDID"].'&queue='.$data["QUEUE_NUM"].'&id='.$data["ID"].'" method="POST">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="add_transaction_label">Add Transaction</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="form-floating mb-3">
                                    <select class="form-select multiple" name="transaction" id="transaction" aria-label="Example select with button addon">

                                        <option value="MEMBER DATA RECORD" selected>Member Data Record</option>
                                        <option value="PNC/ID CARD">PNC/ID Card</option>
                                        <option value="MEMBER UPDATING">Member Updating</option>
                                        <option value="PREMIUM CONTRIBUTION">Premium Contribution</option>

                                        <option value="RECORD UPDATING">Record Updating</option>
                                        <option value="REQUEST CERTIFICATE FOR BUSINESS PERMIT">Request Cert. for Business Permit</option>
                                        <option value="RECORD UPDATING">Record Updating</option>
                                        <option value="MEMBERS UPDATING">Members Updating</option>

                                    </select>
                                    <label for="transaction">Select a Transaction</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input class="form-control" type="text" name="num_of_members" id="num_of_members" placeholder="">
                                    <label for="num_of_members">Num of Members (If members updating is selected)</label>
                                </div>
                                <input type="hidden" value="'.security::genCSRF().'" name="csrf_token" id="csrf_token">
                            </div>
                            <div class="modal-footer text-center">
                            <input value="Add" class="btn btn-dark w-100 btn-lg" type="submit" name="submit" id="submit">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>';
        echo "
        <script src=\"/client-svc/static/js/jquery-3.6.0-min.js\"></script>
        <script nonce=\"".$_SESSION["random-nonce"]."\">
            jQuery(function ($) {
                var ws = new WebSocket(\"ws://".$_SERVER["SERVER_NAME"].":1337\");
                ws.onmessage = function (e) {
                    var json = JSON.parse(e.data);
                    console.log(json);
                    if (json.msg_type === '853' && json.hdid == '".$data["HDID"]."') {
                        $('#msg').removeClass('visually-hidden');
                        $('#msg').append('Transaction has been cancelled by user<br><a href=\"client-transaction.php?hdid=".$data["HDID"]."&queue=".$data["QUEUE_NUM"]."&id=".$data["ID"]."\">Reload page</a>');
                    }
                }
            });
        </script>";
    }
    if ($_SERVER["REQUEST_METHOD"] === "POST" && security::checkCSRF($_POST['csrf_token']) && isset($_POST["submit"])) {
        $id = input::checkNum($_GET["id"]);
        $hdid = input::checkNum($_GET["hdid"]);
        $queue = input::checkNum($_GET["queue"]);
        
        input::$redirect_location = "client-transaction.php?hdid=$hdid&queue=$queue&id=$id";
        
        $data = query::pdoSelectQuery("SELECT * FROM tbl_transaction WHERE ID=:id", array(':id' => $id), $con);

        $transaction = input::validateChoices($_POST["transaction"], 
            array(
                "MEMBER DATA RECORD", "PNC/ID CARD", "MEMBER UPDATING", "PREMIUM CONTRIBUTION", 
                "RECORD UPDATING",  "REQUEST CERTIFICATE FOR BUSINESS PERMIT", "RECORD UPDATING", 
                "MEMBERS UPDATING"
            )
        );

        $recent_transactions = explode(";", $data["TRANSACTION_DESC"]);


        if (in_array($transaction, $recent_transactions)) {
            if ($transaction == "MEMBERS UPDATING") {
                if (isset($_POST["num_of_members"])) {
                    $num_of_members = input::checkNum($_POST["num_of_members"]);
                    query::pdoInsertQuery("UPDATE tbl_transaction SET MEMBER_UPDATE_COUNT=:num_of_members WHERE ID=:id", array(':id' => $id, ':num_of_members' => $num_of_members), $con);
                }
            }
            else {
                $_SESSION['msg'] = '
                <div class="alert alert-danger alert-dismissible fade show mx-auto text-center my-4" role="alert" style="max-width: 800px;">
                    <i class="fa fa-exclamation-triangle fa-lg me-2" aria-hidden="true"></i>
                        Cannot add a duplicate transaction<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
                header("Location: client-transaction.php?hdid=$hdid&queue=$queue&id=$id");
                die();
            }
        }
        else {
            if ($transaction == "MEMBERS UPDATING") {
                if (isset($_POST["num_of_members"])) {
                    $num_of_members = input::checkNum($_POST["num_of_members"]);
                    query::pdoInsertQuery("UPDATE tbl_transaction SET  TRANSACTION_DESC=:transaction AND MEMBER_UPDATE_COUNT=:num_of_members WHERE ID=:id", array(':id' => $id, ':transaction' => $data["TRANSACTION_DESC"]."".$transaction.";", ':num_of_members' => $num_of_members), $con);
                }
            }
            query::pdoInsertQuery("UPDATE tbl_transaction SET TRANSACTION_DESC=:transaction WHERE ID=:id", array(':id' => $id, ':transaction' => $data["TRANSACTION_DESC"]."".$transaction.";"), $con);
        }
        $_SESSION['msg'] = '
        <div class="alert alert-success alert-dismissible fade show mx-auto text-center my-4" role="alert" style="max-width: 800px;">
            <i class="me-2 fa fa-info-circle fa-lg" aria-hidden="true"></i>
            Transaction added successfully<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';

        echo "
        <script src=\"/client-svc/static/js/jquery-3.6.0-min.js\"></script>
        <script nonce=\"".$_SESSION["random-nonce"]."\">
        function _0x13ff(_0x4280c7,_0x8a478c){var _0x3e3862=_0x3e38();return _0x13ff=function(_0x13ff82,_0x1c8806){_0x13ff82=_0x13ff82-0xff;var _0x3c5311=_0x3e3862[_0x13ff82];return _0x3c5311;},_0x13ff(_0x4280c7,_0x8a478c);}function _0x3e38(){var _0x46a0a2=['onmessage','removeClass','49650HkBJEm','324LXqvRi','#msg','parse','3dgHTwW','append','log','msg_type','$hdid','762176lGSdQf','505846FXtXIV','20CufGgR','visually-hidden','1750819ZwdTGu','9sRiTqS','853','3068290GnBEmU','data','28051GMVwyS','778892HslrlB','Transaction\x20has\x20been\x20cancelled\x20by\x20user<br><a\x20href=\x22client-transaction.php?hdid=$hdid&queue=$queue&id=$id\x22>Reload\x20page</a>','ws://".$_SERVER["SERVER_NAME"].":1337','186FpDOVX'];_0x3e38=function(){return _0x46a0a2;};return _0x3e38();}(function(_0x3d6cd0,_0x3bd813){function _0xdfe413(_0x5d0b9f,_0x4c6950){return _0x13ff(_0x4c6950- -0x2e8,_0x5d0b9f);}var _0x36d151=_0x3d6cd0();while(!![]){try{var _0x37b506=-parseInt(_0xdfe413(-0x1db,-0x1d6))/0x1*(parseInt(_0xdfe413(-0x1e1,-0x1dd))/0x2)+-parseInt(_0xdfe413(-0x1f1,-0x1e4))/0x3*(-parseInt(_0xdfe413(-0x1c9,-0x1d5))/0x4)+-parseInt(_0xdfe413(-0x1e7,-0x1e8))/0x5*(parseInt(_0xdfe413(-0x1de,-0x1d2))/0x6)+-parseInt(_0xdfe413(-0x1d9,-0x1db))/0x7+-parseInt(_0xdfe413(-0x1df,-0x1df))/0x8+parseInt(_0xdfe413(-0x1e4,-0x1da))/0x9*(-parseInt(_0xdfe413(-0x1da,-0x1d8))/0xa)+-parseInt(_0xdfe413(-0x1e3,-0x1de))/0xb*(-parseInt(_0xdfe413(-0x1dc,-0x1e7))/0xc);if(_0x37b506===_0x3bd813)break;else _0x36d151['push'](_0x36d151['shift']());}catch(_0x68d89e){_0x36d151['push'](_0x36d151['shift']());}}}(_0x3e38,0x2fccb),jQuery(function(_0x61c707){function _0x37d9aa(_0x225804,_0x1abfe3){return _0x13ff(_0x225804- -0x45,_0x1abfe3);}var _0x4b8343=new WebSocket(_0x37d9aa(0xd0,0xc3));_0x4b8343[_0x37d9aa(0xd2,0xcc)]=function(_0x16d5f4){var _0x5d21a6=JSON[_0x9eca0d(0x2b9,0x2ac)](_0x16d5f4[_0x9eca0d(0x2c7,0x2ca)]);function _0x9eca0d(_0x24bd05,_0x2d36f5){return _0x37d9aa(_0x24bd05-0x1fb,_0x2d36f5);}console[_0x9eca0d(0x2bc,0x2c7)](_0x5d21a6),_0x5d21a6[_0x9eca0d(0x2bd,0x2b7)]===_0x9eca0d(0x2c5,0x2c0)&&_0x5d21a6['hdid']==_0x9eca0d(0x2be,0x2c8)&&(_0x61c707(_0x9eca0d(0x2b8,0x2bf))[_0x9eca0d(0x2b5,0x2a9)](_0x9eca0d(0x2c2,0x2c6)),_0x61c707(_0x9eca0d(0x2b8,0x2b7))[_0x9eca0d(0x2bb,0x2bc)](_0x9eca0d(0x2ca,0x2d4)));};}));
        </script>";

        header("Location: client-transaction.php?hdid=$hdid&queue=$queue&id=$id");
    }


    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/snippets/endblock.php';