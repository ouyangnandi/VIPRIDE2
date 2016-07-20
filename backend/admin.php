<?php

require('init.php');
$action = $_GET["action"];

if ($action == 1) {
    getAdmins();
} else if ($action == 2) {
    createAdmin();
} else if ($action == 3) {
    removeAdmin();
} else if ($action == 4) {
    updateAdmin();
}

function getAdmins() {

    $con = DatabaseConn::getConn();
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $rows = 10;
    $offset = ($page - 1) * $rows;
    $result = array();
    mysqli_query($con, "set names 'utf8'");
    $rs = mysqli_query($con, "select count(1) from admin");
    $row = mysqli_fetch_row($rs);
    $result["total"] = $row[0];
    $rs = mysqli_query($con, "select * from admin order by id desc limit $offset,$rows ");
    $items = array();

    while ($row = mysqli_fetch_object($rs)) {
        array_push($items, $row);
    }
    $result["rows"] = $items;

    echo json_encode($result);
}

function createAdmin() {

    $username = $_REQUEST['admin_username'];
    $password = $_REQUEST['admin_password'];

    $password = md5($password);
    $con = DatabaseConn::getConn();
    mysqli_query($con, "set names 'utf8'");

    $sql = "select * from admin where username = '$username'";
    $rs = mysqli_query($con, $sql);
    if ($row = mysqli_fetch_object($rs)) {
        echo json_encode(array('success' => false, 'msg' => 'Username has already been existed'));
        return;
    }

    $sql = "insert into admin (username,passmd5) values('$username','$password')";
    $result = mysqli_query($con, $sql);
    
    if ($result) {
        echo json_encode(array('success' => true, 'msg' => "Create admin successfully"));
    } else {
        echo json_encode(array('success' => false, 'msg' => 'Create Admin.Database Error.'));
    }
}

function removeAdmin() {

    $id = $_REQUEST['id'];
    $username = $_REQUEST['username'];

    session_start();
    if ($username == $_SESSION['username']) {
        echo json_encode(array('success' => false, 'msg' => 'You can not remove yourself'));
        return;
    }
    $sql = "delete from admin where id=$id";
    $con = DatabaseConn::getConn();

    $result = mysqli_query($con, $sql);
    if ($result) {
        echo json_encode(array('success' => true, 'msg' => "Remove admin successfully"));
    } else {
        echo json_encode(array('success' => false, 'msg' => 'RemoveAdmin. Database Error.'));
    }
}

function updateAdmin() {
    $id = intval($_REQUEST['id']);
    $pass = $_REQUEST['admin_password'];
    $md5Pass = md5($pass);
    $con = DatabaseConn::getConn();
    mysqli_query($con, "set names 'utf8'");
    $sql = "update admin set passmd5='$md5Pass' where id=$id";
    $result = mysqli_query($con, $sql);
    if ($result) {
        echo json_encode(array('success' => true, 'msg' => "Admin details has been changed"));
    } else {
        echo json_encode(array('success' => false, 'msg' => 'Update Admin.Database Error.'));
    }
}
