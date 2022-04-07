<?php
    session_start();

    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/connect_db.php';
    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/security_check.php';
    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/utils/chk_login.php';

    if (!func::checkLoginState($con)) {
        header("Location: login.php");
        die();
    }

    if (security::checkMethod("GET")) {

        input::$redirect_location = "client-transaction.php";

        $fields = array("queue", "hdid");

        input::checkFields($fields, $_GET);

        $queue = input::checkNum($_GET["queue"]);
        $hdid = input::checkNum($_GET["hdid"]);

        $stmt = $con -> prepare('SELECT * FROM tbl_health_declaration WHERE ID=:hdid');
        $stmt -> bindValue(':hdid', $hdid);
        $stmt -> execute();

        if ($stmt -> rowCount() < 1) {
            header("Location: transactions.php");
            die();
        }

        $data = $stmt -> fetch();
            
        $stmt = $con -> prepare("SELECT * FROM tbl_transaction WHERE QUEUE_NUM=:queue_num");
        $stmt -> bindValue(":queue_num", $queue);
        $stmt -> execute();

        if ($stmt -> rowCount() < 1) {
            header("Location: transactions.php");
            die();
        }

        require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/snippets/block-content.php';

        $requestor_name = $data["LASTNAME"].', '.$data["FIRSTNAME"].' '.$data["MIDDLENAME"];

        echo '
        <a href="transactions.php" class="text-decoration-none mb-3">
        <svg xmlns="http://www.w3.org/2000/svg" width="35" height="40" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
        </svg>Back
        </a>
        <div class="bg-white border p-3 my-3">
            <h2 class="text-center">Health Declaration</h2><hr>
            <div class="row justify-content-center">
                <div class="col-lg-5">
                    <h5 class="mt-3">Name</h5>
                    '.$requestor_name.'
                    <h5 class="mt-3">Date & Time Filled</h5>
                    '.$data["DATE_TIME"].'
                    <h5 class="mt-3">Temperature</h5>
                    '.$data["TEMPERATURE"].'
                    <h5 class="mt-3">Address</h5>
                    '.$data["ADDRESS"].'
                    <h5 class="mt-3">Contact Number</h5>
                    '.$data["CONTACT_NUMBER"].'
                </div>
                <div class="col-lg-3">
                    <h5 class="mt-3">Fever</h5>
                    '.$data["FEVER"].'
                    <h5 class="mt-3">Cough</h5>
                    '.$data["COUGH"].'
                    <h5 class="mt-3">Colds</h5>
                    '.$data["COLDS"].'
                    <h5 class="mt-3">Sore Throat</h5>
                    '.$data["SORE_THROAT"].'
                </div>
                <div class="col-lg-3">
                    <h5 class="mt-3">Shortness of Breath</h5>
                    '.$data["SHORTNESS_OF_BREATH"].'
                    <h5 class="mt-3">Diarrhea</h5>
                    '.$data["DIARRHEA"].'
                    <h5 class="mt-3">Frontliner</h5>
                    '.$data["FRONTLINER"].'
                    <h5 class="mt-3">Contact Trace</h5>
                    '.$data["CONTACT_TRACE"].'
                </div>
            </div>
        </div>
        <div class="row justify-content-center">';
        
        $status = "";
        $client_type = "";
        $priority_type = "";

        while ($data2 = $stmt -> fetch()) {
            echo '<div class="col-lg-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <h4 class="card-title">Transaction #'.$data2["ID"].'</h4>
                        <h6 class="card-title">'.$data2["PIN_PEN"].'</h6>
                        <p class="card-text">'.$data2["NAME"].'</p>
                        <a href="client-transaction.php?hdid='.$data2["HDID"].'&queue='.$data2["QUEUE_NUM"].'&id='.$data2["ID"].'" class="btn btn-dark stretched-link mx-auto">Open</a>
                    </div>
                </div>
                </div>';
            $status = $data2["STATUS"];
            $client_type = $data2["CLIENT_TYPE"];
            $priority_type = $data2["PRIORITY"];
        }
        if ($status == "PENDING" || $status == "ON PROCESS") {
            $stmt = $con -> prepare('SELECT * FROM tbl_users WHERE EMPLOYEE_NO=:emp_no');
            $stmt -> bindValue(':emp_no', $_SESSION["username"]);
            $stmt -> execute();

            $data = $stmt -> fetch();
            $counter_num = $data["COUNTER_NUM"];
            $token = security::genCSRF();
            echo '
            <div class="text-center">
                <a href="call-queue-num.php?hdid='.$hdid.'&queue='.$queue.'&priority_type='.$priority_type.'&counter_num='.$counter_num.'&csrf_token='.$token.'" class="btn btn-lg btn-dark my-2">
                    Call Queue Number
                </a>
                <a href="client-transaction-done.php?hdid='.$hdid.'&queue='.$queue.'&csrf_token='.$token.'" class="btn btn-lg btn-dark my-2">
                    Close Transactions
                </a>
            </div>
            ';
            
            echo "
            <script src=\"/client-svc/static/js/jquery-3.6.0-min.js\"></script>
            <script nonce=\"".$_SESSION["random-nonce"]."\">
            function _0x47f9(_0x14ba31,_0x3feab2){var _0x469b74=_0x469b();return _0x47f9=function(_0x47f900,_0x41610e){_0x47f900=_0x47f900-0x16c;var _0x17f212=_0x469b74[_0x47f900];return _0x17f212;},_0x47f9(_0x14ba31,_0x3feab2);}function _0x469b(){var _0x4d74ed=['1502745OKzmCm','toast','48aPVARZ','24aBTAtn','$hdid','ws://".$_SERVER["SERVER_NAME"].":1337','139386uGcyJx','.toast','parse','853','append','857','hdid','onopen','58465BsaMJP','stringify','send','440DByqeo','203YxrcMs','onmessage','239961hHaNkg','726498cSMQbj','2730360lKkThP','This\x20client\x20has\x20changed\x20the\x20status\x20to\x20<span\x20class=\x22badge\x20bg-danger\x20rounded-pill\x20mt-0\x22>CANCELLED</span>','show','A\x20user\x20has\x20already\x20changed\x20this\x20client\x20status\x20to\x20<span\x20class=\x22badge\x20bg-primary\x20rounded-pill\x20mt-0\x22>DONE</span>','340330SEpvQu','msg_type'];_0x469b=function(){return _0x4d74ed;};return _0x469b();}(function(_0x2db1de,_0x7c8927){var _0x5c4d3a=_0x2db1de();function _0x5b62c1(_0x1d0945,_0x5a4f93){return _0x47f9(_0x1d0945-0x15a,_0x5a4f93);}while(!![]){try{var _0x44618e=parseInt(_0x5b62c1(0x2d3,0x2e0))/0x1*(-parseInt(_0x5b62c1(0x2c8,0x2d4))/0x2)+parseInt(_0x5b62c1(0x2d9,0x2d1))/0x3*(-parseInt(_0x5b62c1(0x2c7,0x2ba))/0x4)+-parseInt(_0x5b62c1(0x2e1,0x2ea))/0x5+-parseInt(_0x5b62c1(0x2cb,0x2c1))/0x6*(-parseInt(_0x5b62c1(0x2d7,0x2c9))/0x7)+parseInt(_0x5b62c1(0x2db,0x2e6))/0x8+parseInt(_0x5b62c1(0x2da,0x2e5))/0x9+parseInt(_0x5b62c1(0x2df,0x2d5))/0xa*(parseInt(_0x5b62c1(0x2d6,0x2e4))/0xb);if(_0x44618e===_0x7c8927)break;else _0x5c4d3a['push'](_0x5c4d3a['shift']());}catch(_0x4b5fc5){_0x5c4d3a['push'](_0x5c4d3a['shift']());}}}(_0x469b,0x78dd7),jQuery(function(_0x1e7ccd){var _0x210e74=new WebSocket(_0x3f53c8(-0x2f,-0x31));function _0x3f53c8(_0x54e813,_0x1bee13){return _0x47f9(_0x1bee13- -0x1a1,_0x54e813);}_0x210e74[_0x3f53c8(-0x26,-0x29)]=function(_0x3f6c4b){function _0x2857ea(_0x4aa7ae,_0x5b1622){return _0x3f53c8(_0x4aa7ae,_0x5b1622- -0x1cb);}var _0x40f6bb={'content':'1'};_0x210e74[_0x2857ea(-0x1f7,-0x1f1)](JSON[_0x2857ea(-0x1e5,-0x1f2)](_0x40f6bb));},_0x210e74[_0x3f53c8(-0x28,-0x23)]=function(_0x478df0){function _0x5b038d(_0xa99777,_0x3ed842){return _0x3f53c8(_0xa99777,_0x3ed842- -0x1d8);}var _0x39b0cd=JSON[_0x5b038d(-0x205,-0x206)](_0x478df0['data']);_0x39b0cd['msg_type']===_0x5b038d(-0x20d,-0x205)&&_0x39b0cd[_0x5b038d(-0x20e,-0x202)]==_0x5b038d(-0x20d,-0x20a)&&(_0x1e7ccd(_0x5b038d(-0x207,-0x207))[_0x5b038d(-0x209,-0x20d)](_0x5b038d(-0x1fb,-0x1f6)),_0x1e7ccd('.toast-body')[_0x5b038d(-0x205,-0x204)](_0x5b038d(-0x1f6,-0x1f7))),_0x39b0cd[_0x5b038d(-0x1ef,-0x1f3)]===_0x5b038d(-0x1ff,-0x203)&&_0x39b0cd[_0x5b038d(-0x206,-0x202)]==_0x5b038d(-0x1fc,-0x20a)&&(_0x1e7ccd('.toast')[_0x5b038d(-0x21a,-0x20d)](_0x5b038d(-0x1f8,-0x1f6)),_0x1e7ccd('.toast-body')[_0x5b038d(-0x20c,-0x204)](_0x5b038d(-0x1e7,-0x1f5)));};}));
            </script>";
        }
        if ($status == "PENDING") {
            $frontline_id = (string)$_SESSION["username"];
            $stmt = $con -> prepare("UPDATE tbl_transaction SET STATUS='ON PROCESS', FRONTLINE_ID='$frontline_id' WHERE QUEUE_NUM=:queue_num AND HDID=:hdid");
            $stmt -> bindValue(":queue_num", $queue);
            $stmt -> bindValue(":hdid", $hdid);
            $stmt -> execute();
        }
        
        echo '
            </div>
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div class="toast fade" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false" style="max-width: 400px;">
                <div class="toast-header">
                    <i class="fa fa-lg fa-info-circle"></i>
                    <strong class="ms-2 me-auto">Notification</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body"></div>
            </div>
        </div>
        ';
    }

    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/snippets/endblock.php';