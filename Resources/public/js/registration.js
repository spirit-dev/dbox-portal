$(document).ready(function () {

    initGuesser();

});

function initGuesser() {

    $('#demand_new_user_lastname').on('propertychange input', function (e) {
        var valueChanged = false;
        if (e.type == 'propertychange') {
            valueChanged = e.originalEvent.propertyName == 'value';
        } else {
            valueChanged = true;
        }
        if (valueChanged) {

            $('#username_guess').val(getUsername());
        }
    });
    $('#demand_new_user_firstname').on('propertychange input', function (e) {
        var valueChanged = false;
        if (e.type == 'propertychange') {
            valueChanged = e.originalEvent.propertyName == 'value';
        } else {
            valueChanged = true;
        }
        if (valueChanged) {

            $('#username_guess').val(getUsername());
        }
    });
}

function getUsername() {

    var firstname = $('#demand_new_user_firstname').val().substring(0, 1).toLowerCase();
    var lastname = $('#demand_new_user_lastname').val().toLowerCase();

    return firstname + lastname;
}