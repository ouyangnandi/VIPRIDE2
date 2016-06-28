<?php

require('class.phpmailer.php');
require('init.php');

$firstName = isset($_POST['firstName']) ? $_POST['firstName'] : '';
$lastName = isset($_POST['lastName']) ? $_POST['lastName'] : '';
$phone = isset($_POST['phone']) ? $_POST['phone'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$carType = isset($_POST['typeGroup']) ? $_POST['typeGroup'] : '';
$pickUpDate = isset($_POST['pickUpDate']) ? $_POST['pickUpDate'] : '';
$pickUpTimeRange = isset($_POST['range']) ? $_POST['range'] : '';
$pickUpLocation = isset($_POST['pickUpLocation']) ? $_POST['pickUpLocation'] : '';
$destination = isset($_POST['destination']) ? $_POST['destination'] : '';
$message = isset($_POST['message']) ? $_POST['message'] : '';
$timeCost = isset($_POST['timeCost']) ? $_POST['timeCost'] : '1';
$coupon = isset($_POST['coupon']) ? $_POST['coupon'] : '';
$couponMessage = '';
$status_type;

if ($coupon != '') {
    $sql = "SELECT * FROM coupon WHERE code = '$coupon'" . "and status = 0 ";

    $con = DatabaseConn::getConn();
    $result = mysqli_query($con, $sql);

    if (mysqli_num_rows($result) != 0) {
        $row = mysqli_fetch_object($result);
        $couponMessage = str_replace(" ", " " . $row->coupon_value . " ", $row->coupon_type);
        $status_type = $row->status_type;
    } else {
        echo json_encode(array('status' => '2'));
        return;
    }
}



$fromTime = date("H:i:s", $pickUpTimeRange + 8 * 3600);

$mail = new PHPMailer();
$mail->IsSMTP();
$mail->Host = "smtpout.asia.secureserver.net";
$mail->SMTPAuth = true;
$mail->Port = "80";
$mail->Username = "info@vipride.com.au";
$mail->Password = "info@vipride.com.au";
$mail->Charset = 'UTF-8';
$mail->From = "info@vipride.com.au";
$mail->FromName = "New Order";
$mail->AddAddress("QL@VIPRIDE.com.au", "QL");
$mail->AddAddress("kevin@vipride.com.au", "kevin");
$mail->AddAddress("andy@vipride.com.au", "andy");
$mail->AddAddress("ouyangnandi@hotmail.com", "nandi");

$mail->Subject = 'New Order';
$mail->Body = 'The new order below is coming. Please contact the clien as soon as possible.' . "\n" .
        "First Name: " . $firstName . "\n" .
        "Last Name: " . $lastName . "\n" .
        "Phone: " . $phone . "\n" .
        "Email: " . $email . "\n" .
        "Car Type: " . $carType . "\n" .
        "Pick Up Date: " . $pickUpDate . "\n" .
        "Pick Up Time:" . $fromTime . "\n" .
        "Pick Up Location: " . $pickUpLocation . "\n" .
        "Time cost: " . $timeCost . " Hours \n" .
        "Destination: " . $destination . "\n" .
        "Coupon: " . $couponMessage . "\n" .
        "Message: " . $message . "\n";


if ($mail->Send()) {
    echo json_encode(array('status' => '0'));

    $con = DatabaseConn::getConn();
    mysqli_query($con, "set names 'utf8'");
    if ($status_type == 0) {
        $sql = "update coupon set status='1' , count = count + 1  where code='$coupon'";
        $result = mysqli_query($con, $sql);
    } else {
        $sql = "update coupon SET count = count + 1  where code='$coupon'";
        $result = mysqli_query($con, $sql);
    }
} else {
    echo json_encode(array('status' => '1', 'error' => $mail->ErrorInfo));
}
?>