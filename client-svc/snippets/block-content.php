<?php
    
    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/config.php';
    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/security_check.php';
    
    ob_start("minifier");
    function minifier($code) {
        $search = array(
              
            // Remove whitespaces after tags
            '/\>[^\S ]+/s',
              
            // Remove whitespaces before tags
            '/[^\S ]+\</s',
              
            // Remove multiple whitespace sequences
            '/(\s)+/s',
              
            // Removes comments
            '/<!--(.|\s)*?-->/'
        );
        $replace = array('>', '<', '\\1');
        $code = preg_replace($search, $replace, $code);
        return $code;
    }

    security::genHeaders();

?>
<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Assistance Transaction</title>
    <link rel="stylesheet" href="<?php echo STATIC_URL; ?>css/custom-theme.css">
    <link rel="stylesheet" href="<?php echo STATIC_URL; ?>css/style.css">
    <link rel="stylesheet" href="<?php echo STATIC_URL; ?>css/font-awesome.min.css">
    <script src="<?php echo STATIC_URL; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo STATIC_URL; ?>js/jquery-3.6.0-min.js"></script>
</head>
<noscript><div class="alert alert-danger mx-auto text-center" style="max-width: 590px;"><strong>Attention!</strong> Some parts of this page might break when using <code>&lt;NoScript&gt;</code></div></noscript>
<body class="bg-light d-flex flex-column h-100">
    <?php
        echo '
        <nav id="navbar-main" class="navbar navbar-expand-lg fixed-top border-bottom bg-primary navbar-light">
            <div class="container">
                <div class="navbar-brand mx-auto">
                <img src="/client-svc/static/img/philhealth-logo.png" class="img-fluid" width="180">
                </div>
            </div>
        </nav>';
    ?>
    <div class="container">
    <?php
        if (isset($_SESSION['msg'])) {
            echo $_SESSION['msg'];
            unset($_SESSION['msg']);
        }
    ?>