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

    elseif (!isset($_SESSION["queue_generated"])) {
        header("Location: /client-svc/apps/index/client-type.php");
        die();
    }

    require $_SERVER["DOCUMENT_ROOT"].'/client-svc/utils/connect_db.php';
    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/query.php';
    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/security_check.php';
    
    if (security::checkMethod("GET")) {

        input::$redirect_location = "gen-queue.php";

        $fields = array("queue", "hdid", "csrf_token");

        input::checkFields($fields, $_GET);

        if (security::checkCSRF($_GET['csrf_token'])) {

            $queue = input::checkNum($_GET['queue']);
            $hdid = input::checkNum($_GET['hdid']);

            query::pdoInsertQuery("UPDATE tbl_transaction SET STATUS='CANCELLED' WHERE QUEUE_NUM=:queue_num AND HDID=:hdid", array(":queue_num" => $queue, ":hdid" => $hdid), $con);
            

            $_SESSION["feedback"] = "1";
            unset($_SESSION["queue"]);
            unset($_SESSION["has_transaction"]);
            unset($_SESSION["edit_id"]);
            unset($_SESSION["edit_queue"]);
            unset($_SESSION["member_type"]);
            unset($_SESSION["queue_generated"]);
            
            $_SESSION['msg'] = "
            <div class=\"alert alert-info alert-dismissible fade show mx-auto text-center my-4\" role=\"alert\" style=\"max-width: 800px;\">
                <i class=\"fa fa-info-circle fa-lg me-2\" aria-hidden=\"true\"></i>
                    Transaction has been cancelled
                <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>
            </div>";

            echo "
            <script src=\"/client-svc/static/js/jquery-3.6.0-min.js\"></script>
            <script nonce=\"".$_SESSION["random-nonce"]."\">
            function _0x200b(){var _0x24fe62=['10doYUGg','451072vKNlvQ','".$_SESSION["hdid"]."','193481fvnRxD','onopen','stringify','853','393964rUHtNf','7307736VTnrBl','1790790gogoPU','27HqLKSC','14tqESvp','http://".$_SERVER["SERVER_ADDR"]."/client-svc/apps/index/client-type.php','ws://".$_SERVER["SERVER_ADDR"].":1337','4586QUEgLk','497748OfbPpI','183vkCAJI','11cYTuHr','send'];_0x200b=function(){return _0x24fe62;};return _0x200b();}function _0x2323(_0x32df29,_0x2ab8f6){var _0x200bc4=_0x200b();return _0x2323=function(_0x232394,_0x18f288){_0x232394=_0x232394-0x1c1;var _0x23068b=_0x200bc4[_0x232394];return _0x23068b;},_0x2323(_0x32df29,_0x2ab8f6);}(function(_0x589bef,_0x52e823){var _0x59824d=_0x589bef();function _0x5a75ea(_0x249562,_0x310a97){return _0x2323(_0x310a97- -0x2cd,_0x249562);}while(!![]){try{var _0x583ca1=parseInt(_0x5a75ea(-0x10c,-0x107))/0x1+parseInt(_0x5a75ea(-0xf9,-0xfc))/0x2*(parseInt(_0x5a75ea(-0xf5,-0xfa))/0x3)+-parseInt(_0x5a75ea(-0xff,-0x103))/0x4*(-parseInt(_0x5a75ea(-0x104,-0x10a))/0x5)+parseInt(_0x5a75ea(-0x102,-0xfb))/0x6*(-parseInt(_0x5a75ea(-0x103,-0xff))/0x7)+parseInt(_0x5a75ea(-0x105,-0x109))/0x8*(parseInt(_0x5a75ea(-0x109,-0x100))/0x9)+parseInt(_0x5a75ea(-0x100,-0x101))/0xa+-parseInt(_0x5a75ea(-0x106,-0x10c))/0xb*(parseInt(_0x5a75ea(-0x104,-0x102))/0xc);if(_0x583ca1===_0x52e823)break;else _0x59824d['push'](_0x59824d['shift']());}catch(_0xe004f8){_0x59824d['push'](_0x59824d['shift']());}}}(_0x200b,0x194f9),jQuery(function(_0xca5ba0){var _0x18c4d4=new WebSocket(_0x3a1985(0x2cf,0x2ce));function _0x3a1985(_0x2393a7,_0x2591f3){return _0x2323(_0x2591f3-0xfe,_0x2393a7);}_0x18c4d4[_0x3a1985(0x2c5,0x2c5)]=function(_0x24dc34){var _0x4db19d={'hdid':_0x304f79(0x182,0x179),'msg_type':_0x304f79(0x187,0x17d)};function _0x304f79(_0x481a90,_0x356598){return _0x3a1985(_0x481a90,_0x356598- -0x14a);}_0x18c4d4[_0x304f79(0x179,0x176)](JSON[_0x304f79(0x182,0x17c)](_0x4db19d)),window['location']=_0x304f79(0x17d,0x183);};}));
            </script>";
        }

    }
