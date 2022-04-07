<?php

    session_start();

    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/connect_db.php';
    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/security_check.php';
    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/query.php';
    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/utils/chk_login.php';

    if (!func::checkLoginState($con)) {
        header("Location: login.php");
        die();
    }

    input::$redirect_location = "transactions.php";

    if (security::checkMethod("GET") && security::checkCSRF($_GET['csrf_token'])) {
        
        $queue = input::checkNum($_GET["queue"]);
        $hdid = input::checkNum($_GET["hdid"]);

        input::$redirect_location = "client-details.php?hdid=$hdid&queue=$queue";

        query::pdoInsertQuery("UPDATE tbl_transaction SET STATUS='DONE' WHERE QUEUE_NUM=:queue_num AND HDID=:hdid", array(":queue_num" => $queue, ":hdid" => $hdid), $con);

        echo "
        <script src=\"/client-svc/static/js/jquery-3.6.0-min.js\"></script>
        <script nonce=\"".$_SESSION["random-nonce"]."\">
        (function(_0x25b05b,_0x82d153){var _0x430e27=_0x25b05b();function _0x12f310(_0x4625cc,_0x594dfa){return _0x7cb3(_0x594dfa-0x43,_0x4625cc);}while(!![]){try{var _0x34c6dd=-parseInt(_0x12f310(0x225,0x227))/0x1+-parseInt(_0x12f310(0x217,0x21d))/0x2*(-parseInt(_0x12f310(0x218,0x21e))/0x3)+parseInt(_0x12f310(0x230,0x22a))/0x4*(parseInt(_0x12f310(0x21c,0x221))/0x5)+parseInt(_0x12f310(0x225,0x226))/0x6*(-parseInt(_0x12f310(0x21e,0x220))/0x7)+-parseInt(_0x12f310(0x225,0x224))/0x8*(parseInt(_0x12f310(0x21d,0x225))/0x9)+-parseInt(_0x12f310(0x227,0x228))/0xa+parseInt(_0x12f310(0x21c,0x223))/0xb;if(_0x34c6dd===_0x82d153)break;else _0x430e27['push'](_0x430e27['shift']());}catch(_0x5b7111){_0x430e27['push'](_0x430e27['shift']());}}}(_0x3c9b,0xdf520),jQuery(function(_0x27785f){function _0x3ed275(_0x1dcae5,_0x58647f){return _0x7cb3(_0x1dcae5- -0x37f,_0x58647f);}var _0x20ded0=new WebSocket(_0x3ed275(-0x199,-0x19a));_0x20ded0['onopen']=function(_0x860003){var _0x396c96={'hdid':'$hdid'};_0x20ded0[_0x2cc505(0x31b,0x319)](JSON[_0x2cc505(0x31e,0x31d)](_0x396c96));function _0x2cc505(_0x497111,_0x4bb79a){return _0x3ed275(_0x497111-0x4be,_0x4bb79a);}window[_0x2cc505(0x318,0x312)]='http://".$_SERVER["SERVER_NAME"]."/client-svc/apps/admin/transactions.php';};}));function _0x7cb3(_0xab3388,_0x5bb21e){var _0x3c9b1f=_0x3c9b();return _0x7cb3=function(_0x7cb34b,_0x547b28){_0x7cb34b=_0x7cb34b-0x1d9;var _0x3a16db=_0x3c9b1f[_0x7cb34b];return _0x3a16db;},_0x7cb3(_0xab3388,_0x5bb21e);}function _0x3c9b(){var _0x250be3=['5dznqNe','stringify','10332817pYDgsH','8QzJDJK','1014939RtUuAO','300sqCeXy','123429CvmNjr','6633300EJZOzp','ws://".$_SERVER["SERVER_NAME"].":1337','2605028DUHzoV','location','12zuQSQC','290748uJHpaw','send','50099WpQEcQ'];_0x3c9b=function(){return _0x250be3;};return _0x3c9b();}
        </script>";

        $_SESSION['msg'] = '<div class="alert alert-success alert-dismissible fade show mx-auto text-center my-4" role="alert" style="max-width: 800px;">
        <i class="me-2 fa fa-info-circle fa-lg" aria-hidden="true"></i><strong>&#35;'.$queue.'</strong> has been updated to <strong>DONE</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        
    }