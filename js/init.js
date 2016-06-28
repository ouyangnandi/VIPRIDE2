(function($) {
    $(function() {

        $('.button-collapse').sideNav();
        $('.parallax').parallax();

    }); // end of document ready
})(jQuery); // end of jQuery name space


function openModal(id) {
    $('#'+id + ' :input').val('');
    $('#'+id).openModal();
}

function closeModal(id){
    $('#'+id).closeModal();
}

function generatePager(id, total, pageNum,functionName) {
    var totalPageNumber = Math.ceil(total / 10);
    var htmlStr = "";
    if (pageNum === 1) {
        htmlStr = "<li class='disabled'><a href='#!'><i class='material-icons'>chevron_left</i></a></li>";
    } else {
        htmlStr = " <li class='waves-effect'><a href='#!' onclick='" +functionName +"(" + (pageNum - 1) + ")'><i class='material-icons'>chevron_left</i></a></li>";
    }
    for (var i = 1; i <= totalPageNumber; i++) {
        if (i === pageNum) {
            htmlStr = htmlStr + "<li class='active'><a href='#!' onclick='" +functionName +"(" + i + ")'>" + i + "</a></li>";
        }
        else {
            htmlStr = htmlStr + "<li ><a href='#!' onclick='" +functionName +"(" + i + ")'>" + i + "</a></li>";
        }
    }
    if (pageNum === totalPageNumber) {
        htmlStr = htmlStr + "<li class='disabled'><a href='#!'><i class='material-icons'>chevron_right</i></a></li>";
    } else {
        htmlStr = htmlStr + "<li class='waves-effect'><a href='#!' onclick='" +functionName +"(" + (pageNum + 1) + ")'><i class='material-icons'>chevron_right</i></a></li>";
    }
    $(id).html("");
    $(id).append(htmlStr);

}
