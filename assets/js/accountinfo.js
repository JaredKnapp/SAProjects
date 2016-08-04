$(function () {
    var button = $('#accountinfoButton');
    var box = $('#accountinfoBox');
    var form = $('#accountinfoForm');

    button.removeAttr('href');

    //Show/Hide the accountinfo form
    button.mouseup(function (accountinfo) {
        box.toggle();
        button.toggleClass('active');

        button.find('i.glyphicon').toggleClass('glyphicon-collapse-up', 'glyphicon-collapse-down');
    });

    form.mouseup(function () {
        return false;
    });

    //Hide accountinfo form if clicked anywhere other than on the form
    $(this).mouseup(function (accountinfo) {
        if (!($(accountinfo.target).parent('#accountinfoButton').length > 0)) {
            button.removeClass('active');
            button.find('i.glyphicon').toggleClass('glyphicon-collapse-up', 'glyphicon-collapse-down');
            box.hide();
        }
    });
});