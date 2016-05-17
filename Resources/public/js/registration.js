/*
 * Copyright (c) 2016. Spirit-Dev
 * Licensed under GPLv3 GNU License - http://www.gnu.org/licenses/gpl-3.0.html
 *    _             _
 *   /_`_  ._._/___/ | _
 * . _//_//// /   /_.'/_'|/
 *    /
 *    
 * Since 2K10 until today
 *  
 * Hex            53 70 69 72 69 74 2d 44 65 76
 *  
 * By             Jean Bordat
 * Twitter        @Ji_Bay_
 * Mail           <bordat.jean@gmail.com>
 *  
 * File           registration.js
 * Updated the    17/05/16 21:18
 */

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