$(function () {
    var button = $('#loginButton');
    var box = $('#loginBox');
    var form = $('#loginForm');

    button.removeAttr('href');

    //Show/Hide the login form
    button.mouseup(function (login) {
        box.toggle();
        button.toggleClass('active');

        button.find('i.glyphicon').toggleClass('glyphicon-collapse-up', 'glyphicon-collapse-down');
    });

    form.mouseup(function () {
        return false;
    });

    //Hide login form if clicked anywhere other than on the form
    $(this).mouseup(function (login) {
        if (!($(login.target).parent('#loginButton').length > 0)) {
            button.removeClass('active');
            button.find('i.glyphicon').toggleClass('glyphicon-collapse-up', 'glyphicon-collapse-down');
            box.hide();
        }
    });
});