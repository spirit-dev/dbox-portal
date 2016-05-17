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
 * File           loader.js
 * Updated the    17/05/16 21:18
 */

$(document).ready(function () {

    showLoader();
    setTimeout(hideLoader, 1500);

});

var nbLoader = 0;
var nbLargeLoader = 0;

function hideLoader() {
    nbLoader -= 1;
    if (nbLoader <= 0) {
        $('#gen-loader').addClass('custom-hidden');
    } else {
        setTimeout(function () {
            if (nbLoader > 0) {
                $('#gen-loader').addClass('custom-hidden');
                nbLoader = 0;
            }
        }, 5000);
    }
}

function showLoader() {
    nbLoader += 1;
    $('#gen-loader').removeClass('custom-hidden');
}

function hideLargeLoader() {
    nbLargeLoader -= 1;
    if (nbLargeLoader <= 0) {
        $('#lg-loader').addClass('custom-hidden');
    } else {
        setTimeout(function () {
            if (nbLargeLoader > 0) {
                $('#lg-loader').addClass('custom-hidden');
                nbLargeLoader = 0;
            }
        }, 5000);
    }
}

function showLargeLoader() {
    nbLargeLoader += 1;
    $('#lg-loader').removeClass('custom-hidden');
}