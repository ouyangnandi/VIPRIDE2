<?php
require('init.php');
$action = $_GET["action"];

  if ($action == 1) {
        login();
    } else {
        logout();
    }

function login() {

    $username = $_POST["username"];
    $password = $_POST["password"];

    if (inject_check($username) || inject_check($password)) {
        echo json_encode(array('status' => '0'));
    }

    $password = md5($password);
    $sql = "SELECT 1 FROM admin WHERE username = " . "'$username'" . " and passmd5 = " . "'$password'";

    $con = DatabaseConn::getConn();
    $result = mysqli_query($con, $sql);

    if (mysqli_num_rows($result) != 0) {
         session_start();
         $_SESSION['username'] = "$username";
       echo json_encode(array('status' => '1'));
    } else {
        echo json_encode(array('status' => '0'));
    }
}

function logout() {
    session_start();
    session_unset();
    session_destroy();
    header('Location: index.html');
}

?>