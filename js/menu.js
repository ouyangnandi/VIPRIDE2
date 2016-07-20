
function logout() {
    Cookies.remove('email');
    Cookies.remove('points');
    $(location).attr('href', 'index.html');
}

function loadMenu() {
    if (!Cookies.get("email")) {
        $("#registerLink").show();
        $("#loginLink").show();
        $("#logoutLink").hide();
        $("#userinfoLink").hide();

        $("#registermLink").show();
        $("#loginmLink").show();
        $("#logoutmLink").hide();
        $("#userinfomLink").hide();
    } else {
        $("#registerLink").hide();
        $("#loginLink").hide();
        $("#logoutLink").show();
        $("#userinfoLink").show();

        $("#registermLink").hide();
        $("#loginmLink").hide();
        $("#logoutmLink").show();
        $("#userinfomLink").show();
    }
}
