<?php
    session_start();

    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/connect_db.php';
    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/utils/chk_login.php';

    if (!func::checkLoginState($con)) {
        header("Location: login.php");
        die();
    }

    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/snippets/block-content.php';
    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/query.php';

?>
<ul class="nav justify-content-center">
    <li class="nav-item">
        <a class="nav-link" href="reports.php">
        Reports
        </a>
    </li>
    <li class="nav-item" id="transaction">
        <a class="nav-link border-bottom border-dark" href="transactions.php">
        Transactions
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="my-account.php">
        Account
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="logout.php">
        Logout
        </a>
    </li>
</ul>
<main class="mx-auto my-3 bg-white border p-4" style="min-height: 100vh;">
    <div class="row">
        <div class="col-lg-8">
            <div id="content1"></div>
        </div>
        <div class="col-lg-4">
            <div class="d-flex justify-content-between align-items-start">
                <h6>Total In-Queue</h6>
            </div>
            <div id="content2"></div>
        </div>
    </div>
</main>
<script type="text/javascript" src="<?php echo STATIC_URL; ?>js/form-validation.js"></script>
<script nonce="<?php echo $_SESSION["random-nonce"]; ?>">
(function(_0x2acf70,_0x3eb051){var _0x57d1b=_0x2acf70();function _0x3231e5(_0x5342cd,_0x1d99ba){return _0x3aff(_0x1d99ba-0x18b,_0x5342cd);}while(!![]){try{var _0x174606=-parseInt(_0x3231e5(0x2fd,0x2f8))/0x1+parseInt(_0x3231e5(0x2ef,0x2f9))/0x2*(parseInt(_0x3231e5(0x2f1,0x2f0))/0x3)+parseInt(_0x3231e5(0x2f2,0x2f5))/0x4+-parseInt(_0x3231e5(0x303,0x2ff))/0x5*(-parseInt(_0x3231e5(0x2f3,0x2ef))/0x6)+parseInt(_0x3231e5(0x303,0x2fd))/0x7*(parseInt(_0x3231e5(0x2f0,0x2fb))/0x8)+parseInt(_0x3231e5(0x2ff,0x303))/0x9+-parseInt(_0x3231e5(0x2f5,0x2fe))/0xa;if(_0x174606===_0x3eb051)break;else _0x57d1b['push'](_0x57d1b['shift']());}catch(_0x387524){_0x57d1b['push'](_0x57d1b['shift']());}}}(_0x49d0,0x91714),jQuery(function(_0x467d99){_0x467d99(_0x33525b(0x262,0x26e))[_0x33525b(0x264,0x263)](_0x33525b(0x25f,0x26b)),_0x467d99('#content2')[_0x33525b(0x264,0x260)]('content2.php');var _0x295f43=new WebSocket(_0x33525b(0x255,0x24b));function _0x33525b(_0x434a5e,_0x5d448a){return _0x3aff(_0x434a5e-0xf3,_0x5d448a);}_0x295f43[_0x33525b(0x25a,0x259)]=function(_0x21e524){function _0x2082f4(_0x2af22d,_0x40c793){return _0x33525b(_0x2af22d-0xd,_0x40c793);}console['log'](_0x2082f4(0x268,0x273));},_0x295f43[_0x33525b(0x26a,0x273)]=function(_0x23d62d){function _0x5b5e8d(_0x172306,_0x1e3f46){return _0x33525b(_0x172306- -0x250,_0x1e3f46);}var _0x262c5e=JSON[_0x5b5e8d(0x18,0x11)](_0x23d62d[_0x5b5e8d(0x6,0x1)]);_0x467d99(_0x5b5e8d(0x12,0x16))[_0x5b5e8d(0x14,0x1b)]('content1.php<?php
    if (isset($_GET["status"])) {
        input::$redirect_location = "transactions.php";

        $status = input::validateChoices($_GET["status"], array("PENDING", "ON PROCESS", "DONE", "CANCELLED"));
        echo "?status=".urlencode($status);     
    }
    ?>'),_0x467d99(_0x5b5e8d(0xe,0x14))[_0x5b5e8d(0x14,0xe)]('content2.php');},_0x295f43[_0x33525b(0x26d,0x266)]=function(_0x13c9ac){function _0x3e9411(_0x13a380,_0x2a9030){return _0x33525b(_0x13a380- -0xdb,_0x2a9030);}console[_0x3e9411(0x18e,0x190)](_0x3e9411(0x17e,0x178));},_0x295f43[_0x33525b(0x26c,0x268)]=function(_0x2dfab7){function _0x25ea21(_0x127d8e,_0x503f74){return _0x33525b(_0x503f74-0x1a6,_0x127d8e);}console[_0x25ea21(0x416,0x40f)](_0x25ea21(0x409,0x402));};}));function _0x3aff(_0x3133e9,_0x33bfb0){var _0x49d0e1=_0x49d0();return _0x3aff=function(_0x3affb,_0x5853b8){_0x3affb=_0x3affb-0x162;var _0x50970a=_0x49d0e1[_0x3affb];return _0x50970a;},_0x3aff(_0x3133e9,_0x33bfb0);}function _0x49d0(){var _0xd74f3e=['9215901IEwJOb','onclose','onerror','ws://<?php echo $_SERVER["SERVER_ADDR"]; ?>:1337','data','60oqglXi','303Arcfvs','An\x20error\x20has\x20occurred\x20while\x20connecting\x20to\x20the\x20socket','onopen','Connection\x20Established','Connection\x20Closed','1212344uFgwun','#content2','content1.php<?php
    if (isset($_GET["status"])) {
        input::$redirect_location = "transactions.php";

        $status = input::validateChoices($_GET["status"], array("PENDING", "ON PROCESS", "DONE", "CANCELLED"));
        echo "?status=".urlencode($status);     
    }
    ?>','1173440WPnvKw','20866HQKNRx','#content1','8uhPfNe','load','2096143PGliYv','20898950vrJdUq','589405HAHrTk','parse','log','onmessage'];_0x49d0=function(){return _0xd74f3e;};return _0x49d0();}

    
</script>
<?php
    require_once $_SERVER["DOCUMENT_ROOT"].'/client-svc/snippets/endblock.php';