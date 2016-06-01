<?php

require('class.phpmailer.php');

$firstName = isset($_POST['firstName']) ? $_POST['firstName'] : '';
$lastName = isset($_POST['lastName']) ? $_POST['lastName'] : '';
$phone = isset($_POST['phone']) ? $_POST['phone'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$carType = isset($_POST['typeGroup']) ? $_POST['typeGroup'] : '';
$pickUpDate = isset($_POST['pickUpDate']) ? $_POST['pickUpDate'] : '';
$range = isset($_POST['range']) ? $_POST['range'] : '';
$pickUpLocation = isset($_POST['pickUpLocation']) ? $_POST['pickUpLocation'] : '';
$destination = isset($_POST['destination']) ? $_POST['destination'] : '';
$message = isset($_POST['message']) ? $_POST['message'] : '';

$pickUpTimeRange = explode(";", $range);
$fromTime = date("d-m-Y H:i:s", $pickUpTimeRange[0]);
$toTime = date("d-m-Y H:i:s", $pickUpTimeRange[1]);

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
        "Pick Up Time Range: From " . $fromTime . " To " . $toTime . "\n" .
        "Pick Up Location: " . $pickUpLocation . "\n" .
        "Destination: " . $destination . "\n" .
        "Message: " . $message . "\n";

if ($mail->Send()) {
    echo json_encode(array('status' => '0'));
} else {
    echo json_encode(array('status' => '1', 'error' => $mail->ErrorInfo));
}
?>