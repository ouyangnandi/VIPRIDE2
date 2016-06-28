function getCoupons(pageNum) {
    $("#preloader").show();
    $('#couponTable tbody').html("");
    $.post('backend/coupon.php?action=1', {page: pageNum}, function(result) {
        generatePager("#couponPager", result.total, pageNum, "getCoupons");

        jQuery.each(result.rows, function(index, item) {

            var row = "<tr><td>" + item.code + "</td><td>" + item.coupon_type.replace(" ", " " + item.coupon_value + " ") + "</td>"
                    + "<td>" + (item.status_type == 0 ? "One Off" : "Infinity") + "</td>"
                    + "<td><input type='checkbox' class='filled-in'" + (item.status == 0 ? "" : "checked='checked'") + " id='coupon_" + item.id + "' onchange=\"updateCoupon('" + item.id + "')\"/> <label for='coupon_" + item.id + "'></label></td>"
                    + "<td>" + item.count + "</td>"
                    + "<td><a class='btn-floating red' onclick=\"removeCoupon('" + item.id + "')\"><i class='material-icons'>remove</i></a></td></tr>";

            $('#couponTable tbody:last-child').append(row);
        });
        $("#preloader").fadeOut("slow");
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
    if ($("#couponForm").valid()) {
        var type = $("#coupon_type").val();
        var value = $("#coupon_value").val() === "" ? 1 : $("#coupon_value").val();
        var couponNum = ($("#couponNum").val() === "" ? 0 : $("#couponNum").val());

        $.post('backend/coupon.php?action=2', {type: type, value: value, coupon_num: couponNum}, function(result) {
            if (result.success) {
                Materialize.toast(result.msg, 2000);
                getCoupons(1);
                closeModal("modalCoupon");
            } else {
                alert(result.msg, 2000);
            }

        }, 'json');
    }

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

function setCouponNum(val) {
    $("#couponNum").val(val);
}

function openCouponDialog(id) {
    $("#coupon_value").data("ionRangeSlider").reset();
    $("#oneOff").prop("checked", true);

    openModal(id);
}

function initCouponForm() {

    $("#couponForm").validate({
        rules: {
            coupon_type: "required"
        },
        errorElement: 'div',
        errorPlacement: function(error, element) {
            var placement = $(element).next("span");
            error.addClass("invalid");
            if (placement.length > 0) {
                element.addClass("invalid");
                error.insertAfter(placement);
            } else {
                element.addClass("invalid");
                error.insertAfter(element);
            }
        }
    });
    $("#couponForm").submit(function(e) {
        e.preventDefault();
    });

}

