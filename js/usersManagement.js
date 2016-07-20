function getUsers(pageNum) {
    $("#preloader").show();
    $('#userTable tbody').html("");
    $.post('backend/users.php?action=1', {page: pageNum}, function(result) {
        generatePager("#userPager", result.total, pageNum, "getUsers");

        jQuery.each(result.rows, function(index, item) {

            var row = "<tr><td>" + item.email + "</td>"
                    + "<td><a onclick=\"changePassword(" + item.id + ",'" + item.email + "','" + item.firstName + "','" + item.lastName + "')\" href='#'>Change</a></td>"
                    + "<td>" + item.firstName + "</td>"
                    + "<td>" + item.lastName + "</td>"
                    + "<td>" + item.invitationCode + "</td>"
                    + "<td>" + item.point + "</td>"
                    + "<td><a class='btn-floating red' onclick=\"openUpdateUser(1,'" + item.email + "')\"><i class='material-icons'>edit</i></a> <a class='btn-floating red' onclick=\"removeUser(" + item.id + ")\"><i class='material-icons'>remove</i></a></td></tr>";

            $('#userTable tbody:last-child').append(row);
        });
        $("#preloader").fadeOut("slow");
    }, 'json');

}

function changePassword(id, email, firstName, lastName) {

    $.ajax({
        url: 'backend/users.php?action=5',
        type: 'post',
        dataType: 'json',
        data: {id: id, email: email, firstName: firstName, lastName: lastName},
        success: function(result) {
            if (result.success == "true") {
                Materialize.toast(result.msg, 2000);
            } else if (result.success == "false") {
                Materialize.toast(result.msg, 2000);
            }
            else {
                Materialize.toast(result.msg, 2000);
            }
        },
        error: function(request) {
            console.log(request.responseText);
        }
    });

}

function removeUser(id) {

    $.post('backend/users.php?action=3', {id: id}, function(result) {
        if (result.success) {
            Materialize.toast(result.msg, 2000);
            getUsers(1);
        } else {
            Materialize.toast(result.msg, 2000);
        }

    }, 'json');

}

function createUser() {
    $("#customoverlay").show();
    $("#preloader").show();
    $('#userError').html("");

    var action = 2;

    if ($("#email").attr('readonly') != null && $("#email").attr('readonly') != "") {
        action = 4;
    }

    if ($("#userForm").valid()) {
        $.ajax({
            url: 'backend/users.php?action=' + action,
            type: 'post',
            dataType: 'json',
            data: $("#userForm").serialize(),
            success: function(request) {
                $("#customoverlay").hide();
                $("#preloader").hide();
                if (request.success == true) {
                    alert("Update successfully");
                    $('#modalUser').closeModal();
                    $("#userForm")[0].reset();
                    getUsers(1);
                } else if (request.success == false) {
                    console.log(request.responseText);
                    $('#userError').html(request.msg);
                }
                else {
                    console.log(request.responseText);
                    $('#userError').html(request.msg);
                }
            },
            error: function(request) {
                console.log(request.responseText);
                $('#userError').html(request.msg);
                $("#customoverlay").hide();
                $("#preloader").hide();
            }
        });
    } else {
        $("#customoverlay").hide();
        $("#preloader").hide();
    }
}

function openUpdateUser(type, email) {
    $('#modalUser :input').val('');
    $('#modalUser').openModal();

    if (email != null)
        getDetails(email);

    if (type != 1) {
        $('#email').removeAttr('readonly');
        $('#email').attr("class", "validate");
    }

}

function getDetails(email) {
    $("#customoverlay").show();
    $("#preloader").show();
    $.ajax({
        url: 'backend/registerOnline.php?action=3',
        type: 'post',
        dataType: 'json',
        data: {email: email},
        success: function(request) {
            $("#email").val(request.email);
            $("#firstName").val(request.firstName);
            $("#lastName").val(request.lastName);
            $("#phone").val(request.phone);
            $("#homeAddress").val(request.homeAddress);
            $("#companyAddress").val(request.companyAddress);
            Materialize.updateTextFields();
            $('#email').prop('readonly', true);
            $('#email').removeAttr('class');
            $("#customoverlay").hide();
            $("#preloader").hide();
        },
        error: function(request) {
            console.log(request.responseText);

        }
    });

}

function initUserForm() {

    $("#userForm").validate({
        rules: {
            firstName: {
                required: true
            },
            phone: {
                required: true
            },
            email: {
                required: true
            }
        },
        errorElement: 'div',
        errorPlacement: function(error, element) {
            var placement = $(element).siblings("span");
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
    $("#userForm").submit(function(e) {
        e.preventDefault();
    });

}


