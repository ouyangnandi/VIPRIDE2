function getCoupons(pageNum) {

    $.post('backend/coupon.php?action=1', {page: pageNum}, function(result) {
        generatePager("#couponPager", result.total, pageNum, "getCoupons");
        $('#couponTable tbody').html("");
        jQuery.each(result.rows, function(index, item) {

            var row = "<tr><td>" + item.code + "</td><td>" + item.coupon_type.replace(" ", " " + item.coupon_value + " ")
                    + "</td><td><input type='checkbox' class='filled-in'" + (item.status == 0 ? "" : "checked='checked'") + " id='coupon_" + item.id + "' onchange=\"updateCoupon('" + item.id + "')\"/> <label for='coupon_" + item.id + "'></label></td>"
                    + "<td><a class='btn-floating red' onclick=\"removeCoupon('" + item.id + "')\"><i class='material-icons'>remove</i></a></td></tr>";

            $('#couponTable tbody:last-child').append(row);
        });

    }, 'json');

}

function removeCoupon(id) {

    $.post('backend/coupon.php?action=3', {id: id}, function(result) {
        if (result.success) {
            Materialize.toast(result.msg, 2000);
            getCoupons(1);
        } else {
            Materialize.toast(result.msg, 2000);
        }

    }, 'json');

}

function createCoupon() {

    var type = $("#coupon_type").val();
    var value = $("#coupon_value").val();

    $.post('backend/coupon.php?action=2', {type: type, value: value}, function(result) {
        if (result.success) {
            Materialize.toast(result.msg, 2000);
            getCoupons(1);
            closeModal("modalCoupon");
        } else {
            alert(result.msg, 2000);
        }

    }, 'json');
}

function updateCoupon(id) {

    var checkbox = document.getElementById("coupon_" + id);

    $.post('backend/coupon.php?action=4', {id: id, status: checkbox.checked ? 1 : 0}, function(result) {
        if (result.success) {
            Materialize.toast(result.msg, 2000);

        } else {
            Materialize.toast(result.msg, 2000);
        }

    }, 'json');

}

