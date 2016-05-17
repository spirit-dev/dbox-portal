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
 * File           intro.js
 * Updated the    17/05/16 21:18
 */

/**
 * Created by JuanitoP on 29/10/2015.
 */

$(document).ready(function () {

    console.log('Application ready');

    setTimeout(showSkipButton, 1500);

    setTimeout(callHomepage, 11000);

});

function showSkipButton() {

    $('#skipBtn').removeClass('hidden');

}

function callHomepage() {
    console.log('Change page');
    window.location.href = '/';
}