<?php

require('class.phpmailer.php');
require('init.php');
$register = new registerOnline();
$register->readVariables();
$action = isset($_GET['action']) ? $_GET['action'] : 1;
if ($action == 1) {
    $register->register();
} else if ($action == 2) {
    $register->update();
} else if ($action == 3) {
    $register->getDetails();
}

class registerOnline {

    public $password;
    public $phone;
    public $email;
    public $invitationCode;
    public $firstName;
    public $lastName;
    public $homeAddress;
    public $companyAddress;
    public $code;
    public $inviId;

    public function readVariables() {
        $code = "";
        $inviId = "";
        $this->email = isset($_POST['email']) ? $_POST['email'] : '';
        $this->password = isset($_POST['password']) ? $_POST['password'] : '';
        $this->phone = isset($_POST['phone']) ? $_POST['phone'] : '';
        $this->invitationCode = isset($_POST['invitationCode']) ? $_POST['invitationCode'] : '';
        $this->firstName = isset($_POST['firstName']) ? $_POST['firstName'] : '';
        $this->lastName = isset($_POST['lastName']) ? $_POST['lastName'] : '';
        $this->homeAddress = isset($_POST['homeAddress']) ? $_POST['homeAddress'] : '';
        $this->companyAddress = isset($_POST['companyAddress']) ? $_POST['companyAddress'] : '';
    }

    function register() {
        $passwordMd5 = md5($this->password);
        $con = DatabaseConn::getConn();
        mysqli_query($con, "set names 'utf8'");

        // check duplicated email
        $sql = "select 1 from users where email = '$this->email'";
        $rs = mysqli_query($con, $sql);
        if ($row = mysqli_fetch_object($rs)) {
            echo json_encode(array('success' => "false", 'msg' => 'Email has already been existed'));
            mysqli_free_result($rs);
            return;
        }

        //check invitation code
        if ($this->invitationCode != "") {
            $sql = "select id from users where invitationCode = '$this->invitationCode'";
            $rs = mysqli_query($con, $sql);
            if (!$row = mysqli_fetch_object($rs)) {
                echo json_encode(array('success' => false, 'msg' => 'Invitaion code is invalid'));
                return;
            } else {
                $this->inviId = $row->id;
            }
            mysqli_free_result($rs);
        }
        $uniqueId = uniqid();
// insert into database
        $sql = "insert into users (email,password,phone,invitationCode,homeAddress,companyAddress,firstName,lastName) "
                . " values('$this->email','$passwordMd5','$this->phone','$uniqueId','$this->homeAddress','$this->companyAddress','$this->firstName','$this->lastName')";

        $rs = mysqli_query($con, $sql);

        if (!$rs) {
            $error = mysqli_error($con);
            echo json_encode(array('success' => false, 'msg' => $error . "Save to database failed. Please contact our company by email"));
            return;
        }

// generate the invitation code
        $id = mysqli_insert_id($con);
        $code = $id . "V";
        $codelen = 8; // change as needed

        for ($i = strlen($code); $i < $codelen; $i++) {
            $code .= dechex(rand(0, 15));
        }
        $this->code = strtoupper($code);

        $rs = mysqli_query($con, "UPDATE users SET invitationCode = '" . strtoupper($code) . "' WHERE id = " . $id);

        if ($rs) {

            if ($this->inviId != "") {
                $rs = mysqli_query($con, "UPDATE users SET point = point + 10 WHERE id = " . $this->inviId);
            }

            $this->sendEmail();
            echo json_encode(array('success' => "true", 'msg' => "Register successfully. The register information has been sent to your email address"));
            return;
        } else {
            echo json_encode(array('success' => "false", 'msg' => 'Register failed. Please contact our company by email'));
            return;
        }
    }

    function sendEmail() {
        $mail = getEmailDefaultSettings();
        $mail->AddAddress($this->email, $this->firstName . " " . $this->lastName);

        $mail->Subject = 'Register Successfully';
        $mail->Body = 'Thanks to register our website. Please remeber the details below:' . "\n" .
                "Email: " . $this->email . "\n" .
                "Password: " . $this->password . "\n" .
                "Invitation Code: " . $this->code . "\n" .
                "If your friends register the website by inputing your invitation code, you will earn 10 points which can be used for future offers. ";
        $mail->Send();
    }

    function update() {
        $con = DatabaseConn::getConn();
        mysqli_query($con, "set names 'utf8'");
        $sql = "UPDATE users SET " . "phone = '$this->phone', firstName='$this->firstName', lastName = '$this->lastName', homeAddress='$this->homeAddress', companyAddress='$this->companyAddress'" .
                " WHERE email = '$this->email'";


        $rs = mysqli_query($con, $sql);

        if ($rs) {
            echo json_encode(array('success' => "true", 'msg' => "Update Successfully"));
            return;
        } else {
            echo json_encode(array('success' => "false", 'msg' => 'Update failed.'));
            return;
        }
    }

    function getDetails() {
        $con = DatabaseConn::getConn();
        mysqli_query($con, "set names 'utf8'");
        $rs = mysqli_query($con, "select * from users WHERE email  = '$this->email' ");
        while ($row = mysqli_fetch_object($rs)) {
            echo json_encode($row);
        }
    }

}
