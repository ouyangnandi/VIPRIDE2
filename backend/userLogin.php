<?php

require('class.phpmailer.php');
require('init.php');

$action = $_GET["action"];
$ul = new userLogin();
if ($action == 1) {
    $ul->login();
} else if ($action == 2) {
    $ul->forgetPassword();
} else if ($action == 3) {
    $ul->changePassword();
}

class userLogin {

    function login() {
        $email = $_POST["email"];
        $password = $_POST["password"];

        if (inject_check($email) || inject_check($password)) {
            echo json_encode(array('status' => '0'));
        }

        $password = md5($password);
        $sql = "SELECT * FROM users WHERE email = " . "'$email'" . " and password = " . "'$password'";

        $con = DatabaseConn::getConn();
        $result = mysqli_query($con, $sql);

        if ($row = mysqli_fetch_object($result)) {
            session_start();
            $_SESSION['user'] = "$email";
            echo json_encode(array('status' => '1'));
        } else {
            echo json_encode(array('status' => '0'));
        }
    }

    function forgetPassword() {
        $email = $_POST["emailAddress"];

        if (inject_check($email)) {
            echo json_encode(array('status' => '0'));
        }

        $sql = "SELECT * FROM users WHERE email = " . "'$email'";

        $con = DatabaseConn::getConn();
        $result = mysqli_query($con, $sql);

        if ($row = mysqli_fetch_object($result)) {
            $uniqId = uniqid();
            $password = md5($uniqId);
            $rs = mysqli_query($con, "UPDATE users SET password = '" . $password . "' WHERE email = '$email'");

            if ($rs) {
                $this->sendEmail($email, $row->firstName, $row->lastName, $uniqId);
                echo json_encode(array('status' => '1'));
            } else {
                echo json_encode(array('status' => '0'));
            }
        } else {
            echo json_encode(array('status' => '2'));
        }
    }

    function changePassword() {
        $email = $_POST["emailAddress"];
        $password = $_POST["password"];

        if (inject_check($password)) {
            echo json_encode(array('status' => '0'));
        }

        $sql = "SELECT * FROM users WHERE email = " . "'$email'";

        $con = DatabaseConn::getConn();
        $result = mysqli_query($con, $sql);

        if ($row = mysqli_fetch_object($result)) {
            $password = md5($password);
            $rs = mysqli_query($con, "UPDATE users SET password = '" . $password . "' WHERE email = '$email'");
            if ($rs) {
                echo json_encode(array('status' => '1'));
            } else {
                echo json_encode(array('status' => '0'));
            }
        } else {
            echo json_encode(array('status' => '2'));
        }
    }

    function sendEmail($email, $firstName, $lastName, $newPass) {

        $mail = getEmailDefaultSettings();
        $mail->AddAddress($email, $firstName . " " . $lastName);

        $mail->Subject = 'Password generate successfully';
        $mail->Body = 'Your password has been reset. Please remeber the details below:' . "\n" .
                "Email: " . $email . "\n" .
                "Password: " . $newPass . "\n" .
                "Please login and change your password";
        $mail->Send();
    }

}
