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