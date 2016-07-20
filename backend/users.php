<?php

require('class.phpmailer.php');
require('init.php');
$action = $_GET["action"];

if ($action == 1) {
    getUsers();
} else if ($action == 2) {
    createUser();
} else if ($action == 3) {
    removeUser();
} else if ($action == 4) {
    updateUser();
} else if ($action == 5) {
    changePassword();
}

function getUsers() {

    $con = DatabaseConn::getConn();
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $rows = 10;
    $offset = ($page - 1) * $rows;
    $result = array();
    mysqli_query($con, "set names 'utf8'");
    $rs = mysqli_query($con, "select count(1) from users");
    $row = mysqli_fetch_row($rs);
    $result["total"] = $row[0];
    $rs = mysqli_query($con, "select * from users order by id desc limit $offset,$rows ");
    $items = array();

    while ($row = mysqli_fetch_object($rs)) {
        array_push($items, $row);
    }
    $result["rows"] = $items;

    echo json_encode($result);
}

function createUser() {

    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $firstName = isset($_POST['firstName']) ? $_POST['firstName'] : '';
    $lastName = isset($_POST['lastName']) ? $_POST['lastName'] : '';
    $homeAddress = isset($_POST['homeAddress']) ? $_POST['homeAddress'] : '';
    $companyAddress = isset($_POST['companyAddress']) ? $_POST['companyAddress'] : '';

    $uniqueId = uniqid();
    $password = md5($uniqueId);
    $con = DatabaseConn::getConn();
    mysqli_query($con, "set names 'utf8'");

    // check duplicated email
    $sql = "select 1 from users where email = '$email'";
    $rs = mysqli_query($con, $sql);
    if ($row = mysqli_fetch_object($rs)) {
        echo json_encode(array('success' => false, 'msg' => 'Email has already been existed'));
        mysqli_free_result($rs);
        return;
    }

    // insert into database
    $sql = "insert into users (email,password,phone,invitationCode,homeAddress,companyAddress,firstName,lastName) "
            . " values('$email','$password','$phone','$uniqueId','$homeAddress','$companyAddress','$firstName','$lastName')";
    $rs = mysqli_query($con, $sql);

    if (!$rs) {
        $error = mysqli_error($con);
        echo json_encode(array('success' => false, 'msg' => $error . "Save to database failed. Please contact our developer"));
        return;
    }

    // generate the invitation code
    $id = mysqli_insert_id($con);
    $code = $id . "V";
    $codelen = 8; // change as needed

    for ($i = strlen($code); $i < $codelen; $i++) {
        $code .= dechex(rand(0, 15));
    }
    $code = strtoupper($code);

    $rs = mysqli_query($con, "UPDATE users SET invitationCode = '" . strtoupper($code) . "' WHERE id = " . $id);

    if ($rs) {
        sendEmail($email, $firstName, $lastName, $uniqueId, $code);
        echo json_encode(array('success' => true, 'msg' => "Create successfully. The register information has been sent to your email address"));
        return;
    } else {
        echo json_encode(array('success' => false, 'msg' => 'Create failed. Please contact our company by email'));
        return;
    }
}

function sendEmail($email, $firstName, $lastName, $password, $code) {
    $mail = getEmailDefaultSettings();
    $mail->AddAddress($email, $firstName . " " . $lastName);

    $mail->Subject = 'Register Successfully';
    $mail->Body = 'Thanks to register our website. Please remeber the details below:' . "\n" .
            "Email: " . $email . "\n" .
            "Password: " . $password . "\n" .
            "Invitation Code: " . $code . "\n" .
            "If your friends register the website by inputing your invitation code, you will earn 10 points which can be used for future offers. ";
    $mail->Send();
}

function removeUser() {

    $id = $_REQUEST['id'];
    $sql = "delete from users where id=$id";
    $con = DatabaseConn::getConn();

    $result = mysqli_query($con, $sql);
    if ($result) {
        echo json_encode(array('success' => true, 'msg' => "Remove user successfully"));
    } else {
        echo json_encode(array('success' => false, 'msg' => 'Remove user with Database Error.'));
    }
}

function changePassword() {
    $id = $_POST["id"];
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $firstName = isset($_POST['firstName']) ? $_POST['firstName'] : '';
    $lastName = isset($_POST['lastName']) ? $_POST['lastName'] : '';

    $uniqueId = uniqid();
    $password = md5($uniqueId);
    $con = DatabaseConn::getConn();

    $rs = mysqli_query($con, "UPDATE users SET password = '" . $password . "' WHERE id = '$id'");
    if ($rs) {

        $mail = getEmailDefaultSettings();
        $mail->AddAddress($email, $firstName . " " . $lastName);

        $mail->Subject = 'Password generate successfully';
        $mail->Body = 'Your password has been reset. Please remeber the details below:' . "\n" .
                "Email: " . $email . "\n" .
                "Password: " . $uniqueId . "\n" .
                "Please login and change your password";
        $mail->Send();

        echo json_encode(array('success' => true, 'msg' => "Change password successfully"));
    } else {
        echo json_encode(array('success' => false, 'msg' => 'Change password with Database Error.'));
    }
}

function updateUser() {

    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $point = isset($_POST['point']) ? $_POST['point'] : '';
    $firstName = isset($_POST['firstName']) ? $_POST['firstName'] : '';
    $lastName = isset($_POST['lastName']) ? $_POST['lastName'] : '';
    $homeAddress = isset($_POST['homeAddress']) ? $_POST['homeAddress'] : '';
    $companyAddress = isset($_POST['companyAddress']) ? $_POST['companyAddress'] : '';

    $con = DatabaseConn::getConn();
    mysqli_query($con, "set names 'utf8'");

    $sql = "UPDATE users SET " . "phone = '$phone', firstName='$firstName', lastName = '$lastName', homeAddress='$homeAddress', companyAddress='$companyAddress',point='$point'" .
            " WHERE email = '$email'";
    $result = mysqli_query($con, $sql);
    if ($result) {
        echo json_encode(array('success' => true, 'msg' => "User details has been changed"));
    } else {
        echo json_encode(array('success' => false, 'msg' => 'Update user with database Error.'));
    }
}
