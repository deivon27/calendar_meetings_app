var root = "../../";

/**
 * Redirect with delay
 * @param page
 * @param delay
 */
function delayedRedirect(page, delay) {
    setTimeout(function () {
        window.location.href = page;
    }, parseInt(delay) * 1000);
}

/**
 * Response handler
 * @param elResp
 * @param html
 * @param type
 */
function response(elResp, html, type) {
    elResp.removeClass('reded greened greyed');
    if (type == 'error') {
        $(elResp).html(html).addClass('reded');
    } else {
        $(elResp).html(html).addClass('greened');
    }
    elResp.slideDown();
}

/**
 * Preloader handler
 * @param el
 */
function preloader(el) {
    el.addClass('greyed');
    el.html('Please wait...').slideDown();
}

/**
 * Sign In function
 * @returns {boolean}
 */
function signIn() {
    var loginForm = $('#loginForm');
    var login = $('#login-email').val();
    var pass = $('#login-pass').val();
    if (login != "" && pass != "") {
        var resp = loginForm.find('.response');
        preloader(resp);
        $.ajax({
            type: "POST",
            url: root + "handlers/auth/login_user.php",
            data: loginForm.serialize(),
            success: function (r) {
                if (r == 1) {
                    response(resp, 'Please wait...', 'success');
                    delayedRedirect(root + "index.php", 3);
                } else {
                    response(resp, 'Some data is incorrect. Try again', 'error');
                }
            },
        });
    }
    return false;
}

/**
 * Sign Up / Registration function
 * @returns {boolean}
 */
function signUp() {
    var regForm = $('#registrationForm');
    var pass = $('#login-pass').val();
    var rpass = $('#rlogin-pass').val();
    if (pass != "" && rpass != "") {
        var resp = regForm.find('.response');
        preloader(resp);
        $.ajax({
            type: "POST",
            url: root + "handlers/auth/register_user.php",
            data: regForm.serialize(),
            success: function (r) {
                if (r == 1) {
                    response(resp, 'Please wait...', 'success');
                    delayedRedirect("login.php", 3);
                } else if (r == 2) {
                    response(resp, "Passwords do not match" , 'error');
                } else {
                    response(resp, 'Some data is incorrect. Try again', 'error');
                }
            },
        });
    }
    return false;
}

/**
 * Switch to Admin Sign Up View
 */
function switchSignUpAdmin() {
    var _parent = $('.login-screen');
    _parent.find('.header').fadeOut(300, function () {
        $(this).html('Admin Sign Up').fadeIn(300);
    });
    _parent.find('#loginForm').fadeOut(300, function () {
        $(this).fadeIn(300);
    });
    _parent.find('.btn').not('.admin-signup').fadeOut(300, function () {
        _parent.find('.admin-signup').css('display', 'block');
        _parent.find('.admin-signup').fadeIn(300);
    });
}

/**
 * Admin Sign Up function
 * @returns {boolean}
 */
function signUpAdmin() {
    var regForm = $('#loginForm');
    var login = $('#login-email').val();
    var pass = $('#login-pass').val();
    if (login != "" && pass != "") {
        var resp = regForm.find('.response');
        preloader(resp);
        $.ajax({
            type: "POST",
            url: root + "handlers/auth/register_admin.php",
            data: regForm.serialize(),
            success: function (r) {
                if (r == 1) {
                    signIn();
                } else {
                    response(resp, 'Some data is incorrect. Try again', 'error');
                }
            },
        });
    }
    return false;
}

/** Create trigger to submit the form on Enter press ******/
$('#login-pass').on('keyup', function (e) {
    if (e.which === 13) {
        signIn();
    }
});

/** Create trigger to submit the form on Enter press ******/
$('#rlogin-pass').on('keyup', function (e) {
    if (e.which === 13) {
        signUp();
    }
});