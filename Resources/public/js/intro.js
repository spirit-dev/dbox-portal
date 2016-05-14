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