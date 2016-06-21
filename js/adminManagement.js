function getAdmins(pageNum) {

    $.post('backend/admin.php?action=1', {page: pageNum}, function(result) {
        generatePager("#adminPager", result.total, pageNum,"getAdmins");
        $('#adminTable tbody').html("");
        jQuery.each(result.rows, function(index, item) {

            var row = "<tr><td>" + item.username + "</td>" + "<td><a onclick=\"openUpdateAdmin('" + item.username + "'," + item.id + ")\" href='#'>Change</a></td><td><a class='btn-floating red' onclick=\"removeAdmin(" + item.id + ",'" + item.username + "')\"><i class='material-icons'>remove</i></a></td></tr>";

            $('#adminTable tbody:last-child').append(row);
        });

    }, 'json');

}

function removeAdmin(id, username) {

    $.post('backend/admin.php?action=3', {id: id, username: username}, function(result) {
        if (result.success) {
            Materialize.toast(result.msg, 2000);
            getAdmins(1);
        } else {
            Materialize.toast(result.msg, 2000);
        }

    }, 'json');

}

function createAdmin() {

    var userName = $("#admin_username").val();
    var password = $("#admin_password").val();
    $('#admin_username').prop('disabled', false);
    $.post('backend/admin.php?action=2', {admin_username: userName, admin_password: password}, function(result) {
        if (result.success) {
            Materialize.toast(result.msg, 2000);
            getAdmins(1);
            closeModal("modalAdmin");
        } else {
            alert(result.msg, 2000);
        }

    }, 'json');
}

function updateAdmin(id) {
    var password = $("#admin_password").val();
    $.post('backend/admin.php?action=4', {id: id, admin_password: password}, function(result) {
        if (result.success) {
            Materialize.toast(result.msg, 2000);
            getAdmins(1);
            closeModal("modalAdmin");
        } else {
            Materialize.toast(result.msg, 2000);
        }

    }, 'json');


}

function openUpdateAdmin(userName, id) {
    $('#modalAdmin :input').val('');
    $('#modalAdmin').openModal();
    $('#admin_username').val(userName);
    $('#admin_username').prop('disabled', true);
    $('#admin_id').val(id);
}

function adminOpeartion() {

    if ($('#admin_id').val() === "") {
        createAdmin();
    } else {
        updateAdmin($('#admin_id').val());
    }

}



