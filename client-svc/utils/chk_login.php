<?php

class func {

    public static function checkLoginState($db) {
        if (isset($_COOKIE['username']) && isset($_COOKIE['token']) && isset($_COOKIE['serial'])) {

            $employee_no = $_COOKIE['username'];
            $token = $_COOKIE['token'];
            $serial = $_COOKIE['serial'];
            
            $stmt = $db -> prepare('SELECT * FROM tbl_sessions WHERE EMPLOYEE_NO=:employee_no AND TOKEN=:token AND SERIAL=:serial');
            $stmt -> bindValue(":employee_no", $employee_no);
            $stmt -> bindValue(":token", $token);
            $stmt -> bindValue(":serial", $serial);

            $stmt -> execute();

            $row = $stmt -> fetch(PDO::FETCH_ASSOC);

            if ($row['EMPLOYEE_NO'] > 0) {
                if (
                    $row['EMPLOYEE_NO'] == $_COOKIE['username'] &&
                    $row['TOKEN'] == $_COOKIE['token'] &&
                    $row['SERIAL'] == $_COOKIE['serial']
                    ) {
                    if (
                        $row['EMPLOYEE_NO'] == $_SESSION['username'] &&
                        $row['TOKEN'] == $_SESSION['token'] &&
                        $row['SERIAL'] == $_SESSION['serial']
                    ) {
                        return true;
                    }
                    else {
                        func::createSession($_COOKIE['username'], $_COOKIE['token'], $_COOKIE['serial']);
                        return true;
                    }
                }
            }
        }
    }

    public static function createRecord($db, $username) {
        $stmt = $db -> prepare('DELETE FROM tbl_sessions WHERE EMPLOYEE_NO=:employee_no');
        $stmt -> bindValue(':employee_no', $username);
        $stmt -> execute();

        $token = bin2hex(openssl_random_pseudo_bytes(16));
        $random_letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $serial = substr(str_shuffle($random_letters), 0, 64);

        func::createCookie($username, $token, $serial);
        func::createSession($username, $token, $serial);
        
        $stmt = $db -> prepare('INSERT INTO tbl_sessions (EMPLOYEE_NO, TOKEN, SERIAL)
        VALUES (:employee_no, :token, :serial)');
        $stmt -> bindValue(':employee_no', $username);
        $stmt -> bindValue(':token', $token);
        $stmt -> bindValue(':serial', $serial);
        $stmt -> execute();
    }

    public static function createCookie($username, $token, $serial) {
        setcookie('username', $username, time() + (86400) * 30, '/', null, false, true);
        setcookie('token', $token, time() + (86400) * 30, '/', null, false, true);
        setcookie('serial', $serial, time() + (86400) * 30, '/', null, false, true);
    }

    public static function createSession($username, $token, $serial) {
        $_SESSION['username'] = $username;
        $_SESSION['token'] = $token;
        $_SESSION['serial'] = $serial;
    }

    public static function deleteCookie() {
        setcookie('username', '', time() - 1, '/', null, false, true);
        setcookie('token', '', time() + - 1, '/', null, false, true);
        setcookie('serial', '', time() + - 1, '/', null, false, true);
    }

    public static function deleteSession() {
        unset($_SESSION['username']);
        unset($_SESSION['token']);
        unset($_SESSION['serial']);
    }
}