<?php

    // Database Credentials

    define("DB_HOST", 'localhost');
    define("DB_DATABASE", 'clients_database');
    define("DB_USERNAME", 'root');
    define("DB_PASSWORD", '');

    // RDP Acc
    // U: ITMS
    // P: ITMS@procar

    define("DB_HOST_TWO", '172.22.129.253,49679');
    define("DB_DATABASE_TWO", 'PROCAR_QUEUING');
    define("DB_USERNAME_TWO", 'sa');
    define("DB_PASSWORD_TWO", 'ITMS@procar');

    // Global Variables
    
    define("STATIC_URL", "/client-svc/static/");

    define("STATIC_URL_CSS", "/client-svc/static/css");
    define("STATIC_URL_IMG", "/client-svc/static/img");
    define("STATIC_URL_JS", "/client-svc/static/js");
    
    // define("BASE_URL", dirname(dirname(__FILE__)));

    define("block", $_SERVER['DOCUMENT_ROOT'].'/client-svc/snippets/block-content.php');
    define("endblock", $_SERVER['DOCUMENT_ROOT'].'/client-svc/snippets/endblock.php');
    
    // Allow everything but only from the same origin
    // default-src 'self';
    // Only Allow Scripts from the same origin
    // script-src 'self';
    // Allow Google Analytics, Google AJAX CDN and Same Origin
    // script-src 'self' www.google-analytics.com ajax.googleapis.com;
    
    // Deny: This directive stops the site from being rendered in <frame> i.e. site can’t be embedded into other sites.
    // Sameorigin: This directive allows the page to be rendered in the frame iff frame has the same origin as the page.
    
    // header("X-Frame-Options: Deny");

    // header("X-Content-Type-Options: nosniff");
    // header("X-XSS-Protection: 1; mode=block");
    // header("Strict-Transport-Security: max-age=63072000; includeSubDomains");
    // header("Referrer-Policy: strict-origin-when-cross-origin");
    

    // Turn error reporting to off when used in production

    // ini_set('display errors', 'Off');
    error_reporting(0);
    
    // You can also edit other security configs located at: xampp/php/php.ini

    // session.cookie_httponly=true or session.cookie_httponly=1
    // session.cookie_secure=1
    // expose_php=off
    // session.cookie_samesite="Lax"

    // PDO MSSQL Installation
    // https://docs.microsoft.com/en-us/sql/connect/php/system-requirements-for-the-php-sql-driver?redirectedfrom=MSDN&view=sql-server-ver15

    // PHP Version: 7.1.8

    // Working Drivers:
    // php_pdo_sqlsrv_71_ts_x86.dll
    // php_sqlsrv_71_ts_x86.dll

    // Installing socket.io (Don't mind this)
    // npm install --save express
    // npm install --save socket.io
    // npm install --save winson // For Debugging purposes

    // The installed composer is located at xampp/php/
    // php composer.phar require wisembly/elephant.io -d ../htdocs/client-svc/.......

    // Installing ratchet socket
    // Requirements: composer, php executable

    // Installing composer
    // php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    // php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
    // php composer-setup.php
    // php -r "unlink('composer-setup.php');"

    // Installing ratchet
    // php composer.phar require cboden/ratchet

    // <?php
    // require __DIR__ . '/vendor/autoload.php';