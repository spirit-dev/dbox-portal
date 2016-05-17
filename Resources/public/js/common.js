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
 * File           common.js
 * Updated the    17/05/16 21:18
 */

/**
 * Created by JuanitoP on 29/10/2015.
 */

$(document).ready(function () {

    console.log('Application ready');

    // Initialize tooltips
    $('.tooltiped').tooltip();

    // Call for retrieving communications
    //showCommunications();

    initFeedback();

});

/**
 * Toogle left navigation bar size (responsive)
 */
function toggleNav(direction) {

    var centralContent = $('#central-content');
    var leftContent = $('#left-content');

    var sidebar = $('#sidebar');

    var pushLeft = $('#push-left');
    var pushRight = $('#push-right');

    if (direction === "right") {
        // Expand
        centralContent.removeClass("col-sm-10");
        centralContent.removeClass("col-md-11");
        centralContent.addClass("col-sm-9");
        centralContent.addClass("col-md-10");

        leftContent.removeClass("col-sm-2");
        leftContent.removeClass("col-md-1");
        leftContent.addClass("col-sm-3");
        leftContent.addClass("col-md-2");

        sidebar.removeClass('sidebar-collapsed');
        sidebar.addClass('sidebar-expanded');

        $('.left-menu-item-text').css("display", "inline-block");
    }

    if (direction === "left") {
        // Collapse
        centralContent.removeClass("col-sm-9");
        centralContent.removeClass("col-md-10");
        centralContent.addClass("col-sm-10");
        centralContent.addClass("col-md-11");

        leftContent.removeClass("col-sm-3");
        leftContent.removeClass("col-md-2");
        leftContent.addClass("col-sm-2");
        leftContent.addClass("col-md-1");

        sidebar.removeClass('sidebar-expanded');
        sidebar.addClass('sidebar-collapsed');

        $('.left-menu-item-text').attr('style', 'display: none !important');

    }
}

/**
 * Change clonable url
 * @param switchTo
 */
function switchCloneUrl(switchTo) {

    var btn_ssh = '#btn-clone-uri-ssh';
    var btn_http = '#btn-clone-uri-http';
    var input_clone_url = '#clone-url';

    if (switchTo === 'http') {
        $(btn_ssh).removeClass('active');
        $(btn_http).addClass('active');
        $(input_clone_url).val($('#http-url').val());
    }
    if (switchTo === 'ssh') {
        $(btn_http).removeClass('active');
        $(btn_ssh).addClass('active');
        $(input_clone_url).val($('#ssh-url').val());
    }

}

/**
 * Send Post request inpage with reload follow
 * @param path
 * @param params
 * @param method
 */
function post(path, params, method) {
    method = method || "post"; // Set method to post by default if not specified.

    // The rest of this code assumes you are not using a library.
    // It can be made less wordy if you use one.
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);

    for (var key in params) {
        if (params.hasOwnProperty(key)) {
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", params[key]);

            form.appendChild(hiddenField);
        }
    }

    document.body.appendChild(form);
    form.submit();
}

/**
 * Reload page with timered
 * @param timer
 */
function reloadPage(timer) {
    // If timer is null, reload directly
    if (typeof timer === 'undefined') location.reload();
    // Reload page with timer
    setTimeout(function () {
        location.reload();
    }, timer);
}

function initFeedback() {
    var feedbackBtn = $('#feedback-btn');

    feedbackBtn.click(function () {

        bootbox.prompt("Send us a feedback about EcosystemV2", function (result) {
            if (result !== null && result !== "") {
                showLoader();
                $.ajax({
                    type: 'POST',
                    url: '/feedbacks/register',
                    data: {
                        content: result
                    }
                }).done(function (data) {
                    showCommunication('Thank you', 'Thank you for this feedback :)', 'success');
                }).fail(function (data) {
                    showCommunication('Feedback error', 'Sorry an error occured while registering your feedback :(', 'error');
                }).always(function () {
                    hideLoader();
                });
            }
        });

    });
}