<?php

class input {

    static $redirect_location = "";
    static $max_characters = 250;
    
    public static function validateChoices($input, $array_choices) {
        $checkedInput = "";
        if (in_array(strtoupper($input), $array_choices)) {
            $checkedInput = strtoupper($input);
        }
        else {
            header("Location: ".input::$redirect_location);
            die();
        }
        return $checkedInput;
    }

    public static function sanitizeInput($input) {
        $sanitizedInput = "";
        if (input::validateInput($input)) {
            $sanitizedInput = trim($input);
            $sanitizedInput = stripslashes($input);
            $sanitizedInput = htmlspecialchars(strtoupper($input), ENT_QUOTES, 'UTF-8');
        }
        return $sanitizedInput;
    }

    public static function checkEmail($input) {
        $regex_email = "/^[a-zA-Z0-9_.-]+@[a-zA-Z]+(?:\.[a-zA-Z]+)*$/";
        $emailInput = "";
        if (!empty($input)) {
            if (preg_match($regex_email, $input) && strlen($input) <= input::$max_characters) {
                $emailInput = $input;
            }
            else {
                header("Location: ".input::$redirect_location);
                die();
            }
        }
        return $emailInput;
    }

    public static function checkNum($input) {
        $numInput = "";
        if (input::validateInput($input)) {
            if (ctype_digit((string)$input)) {
                $numInput = $input;
            }
            else {
                header("Location: ".input::$redirect_location);
                die();
            }
        }
        return $numInput;
    }

    public static function checkDate($input) {
        $dateInput = "";
        if (input::validateInput($input)) {
            $date_arr = explode('-', $input);
            if (count($date_arr) == 3) {
                if (checkdate($date_arr[1], $date_arr[2], $date_arr[0])) {
                    $dateInput = $date_arr[0].'-'.$date_arr[1].'-'.$date_arr[2];
                } else {
                    header("Location: ".input::$redirect_location);
                    die();
                }
            }
            else {
                header("Location: ".input::$redirect_location);
                die();
            }
        }
        return $dateInput;
    }

    public static function checkDecimal($input) {
        $decimalInput = "";
        if (input::validateInput($input)) {
            if (is_numeric((string)$input)) {
                $decimalInput = $input;
            }
            else {
                header("Location: ".input::$redirect_location);
                die();
            }
        }
        return $decimalInput;
    }

    public static function validateInput($input) {
        $flag = false;
        if (!empty($input)) {
            if (strlen($input) <= input::$max_characters) {
                $flag = true;
            }
            else {
                header("Location: ".input::$redirect_location);
                die();
            }
        }
        else {
            header("Location: ".input::$redirect_location);
            die();
        }
        return $flag;
    }

    public static function checkFields($field_to_check, $method) {
        foreach ($field_to_check as $field_value) {
            if (!in_array($field_value, array_keys($method))) {
                header("Location: ".input::$redirect_location);
                die();
            }
        }
    }

    public static function errorRedirect($msg) {
        $_SESSION['msg'] = "
        <div class=\"alert alert-danger alert-dismissible fade show mx-auto text-center my-4\" role=\"alert\" style=\"max-width: 800px;\">
            <i class=\"fa fa-exclamation-triangle fa-lg me-2\" aria-hidden=\"true\"></i>
                $msg
            <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>
        </div>";
        header("Location: ".input::$redirect_location);
        die();
    }
}

class security {

    public static function genHeaders() {

        $_SESSION["random-nonce"] = bin2hex(openssl_random_pseudo_bytes(16));
        date_default_timezone_set("Asia/Manila");

        header('Content-Type: text/html; charset=utf-8');
        header("Content-Security-Policy: script-src 'self' 'nonce-".$_SESSION["random-nonce"]."'");

    }

    public static function genCSRF() {
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(16));            
        return $_SESSION['csrf_token'];
    }

    public static function checkCSRF($csrf_token) {
        $flag = false;
        if (isset($_SESSION['csrf_token']) && $csrf_token == $_SESSION['csrf_token']) {
            $flag = true;
        }
        else {
             header("Forbidden", true, 403);
             die("<h1>Invalid CSRF Token</h1>");
        }
        return $flag;
    }

    public static function checkMethod($req_method) {
        $flag = false;
        if ($_SERVER["REQUEST_METHOD"] === $req_method) {
            $flag = true;
        }
        else {
            header("Method Not Allowed", true, 405);
            die("<h1>Not Allowed</h1>");
        }
        return $flag;
    }
}