<?php

require('init.php');
$action = $_GET["action"];

if ($action == 1) {
    getCoupons();
} else if ($action == 2) {
    createCoupon();
} else if ($action == 3) {
    removeCoupon();
} else if ($action == 4) {
    updateCoupon();
}

function getCoupons() {

    $con = DatabaseConn::getConn();
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $rows = 10;
    $offset = ($page - 1) * $rows;
    $result = array();
    mysqli_query($con, "set names 'utf8'");
    $rs = mysqli_query($con, "select count(1) from coupon");
    $row = mysqli_fetch_row($rs);
    $result["total"] = $row[0];
    $rs = mysqli_query($con, "select * from coupon order by id desc limit $offset,$rows ");
    $items = array();

    while ($row = mysqli_fetch_object($rs)) {
        array_push($items, $row);
    }
    $result["rows"] = $items;

    echo json_encode($result);
}

function createCoupon() {

    $option = $_REQUEST['type'];
    $value = isset($_REQUEST['value']) ? intval($_REQUEST['value']):1;
    $status_type = isset($_REQUEST['coupon_num']) ? intval($_REQUEST['coupon_num']):0;

    $con = DatabaseConn::getConn();

    $sql = "insert into coupon (coupon_type,coupon_value,status,code,count,status_type) values('" . $option . "'," . $value . ",0,'',0,$status_type)";
    $result = mysqli_query($con, $sql);

    if (!$result) {
        echo json_encode(array('success' => false, 'msg' => "Create coupon. Database Error."));
        return;
    }

    $id = mysqli_insert_id($con);
    $code = $id . "V";
    $codelen = 6; // change as needed

    for ($i = strlen($code); $i < $codelen; $i++) {
        $code .= dechex(rand(0, 15));
    }

    $result = mysqli_query($con, "UPDATE coupon SET code = '" . strtoupper($code) . "' WHERE id = " . $id);

    if ($result) {
        echo json_encode(array('success' => true, 'msg' => "Create coupon successfully"));
    } else {
        echo json_encode(array('success' => false, 'msg' => 'Create coupon--insert code.Database Error.'));
    }
}

function removeCoupon() {

    $id = $_REQUEST['id'];
    $sql = "delete from coupon where id=$id";
    $con = DatabaseConn::getConn();
    $result = mysqli_query($con, $sql);
    if ($result) {
        echo json_encode(array('success' => true, 'msg' => "Remove coupon successfully"));
    } else {
        echo json_encode(array('success' => false, 'msg' => 'Remove coupon. Database Error.'));
    }
}

function updateCoupon() {
    $id = intval($_REQUEST['id']);
    $status = $_REQUEST['status'];
    $con = DatabaseConn::getConn();
    mysqli_query($con, "set names 'utf8'");
    $sql = "update coupon set status='$status' where id=$id";
    $result = mysqli_query($con, $sql);
    if ($result) {
        echo json_encode(array('success' => true, 'msg' => "Coupon details has been changed"));
    } else {
        echo json_encode(array('success' => false, 'msg' => 'Update Coupon.Database Error.'));
    }
}

?>
