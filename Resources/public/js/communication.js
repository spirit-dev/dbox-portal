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
 * File           communication.js
 * Updated the    19/05/16 15:49
 */

$(document).ready(function () {

    // Extracting current url
    pathArray = location.href.split('/');
    console.log(pathArray);

    // If we are in a project page
    if (pathArray[3] === "project") {
        // Ask communications for this project
        showCommunications(pathArray[4]);
    }

    // Call for retrieving communications
    showCommunications('global');

});

function showCustomCommunication(customContent) {
    new PNotify(customContent);
}

/**
 * Show unique communication
 * @param title
 * @param text
 * @param type
 */
function showCommunication(title, text, type) {
    new PNotify({
        title: title,
        text: text,
        type: type
    });
}

/**
 * Reach communications and
 */
function showCommunications(scope) {
    showLoader();
    // Post request
    $.post("/communication/available/" + scope, function (data) {
        var communications = data.communications;
        // If datas available
        if (communications.length > 0) {
            // Loop on each available datas
            for (var i = 0; i < communications.length; i++) {

                // Define content design (with -remove- button)
                var textPN = '<p>' + communications[i].content + '</p><p id="com-slot-' + communications[i].id + '"><button class="btn btn-vsm btn-primary pull-right" onclick="setCommunicationViewed(\'' + communications[i].id + '\');">Hide away</button></p>';

                showCommunication(communications[i].title, textPN, communications[i].type);

            }
        }
        hideLoader();
    });
}

/**
 * Switch -remove- button to -cancel- button
 * @param comId
 */
function setCommunicationViewed(comId) {
    // Get slot where button is placed
    var slot = '#com-slot-' + comId;
    // Set loading state
    $(slot).children().button('loading');
    // Send -remove- post request
    $.post("/communication/setviewed", {com_id: comId}, function (data) {
        // Remove -remove- button
        $(slot).children().remove();
        // Replace by -cancel- button
        var cancelBtn = '<button class="btn btn-vsm btn-primary pull-right" onclick="cancelCommunicationViewed(\'' + comId + '\');">Cancel</button>';
        $(slot).append(cancelBtn);
    });
}

/**
 * Switch -cancel- button to -remove- button
 * @param comId
 */
function cancelCommunicationViewed(comId) {
    // Get slot where button is placed
    var slot = '#com-slot-' + comId;
    // Set loading state
    $(slot).children().button('loading');
    // Send -remove- post request
    $.post("/communication/setunviewed", {com_id: comId}, function (data) {
        // Remove -cancel- button
        $(slot).children().remove();
        // Replace by -remove- button
        var setViewedBtn = '<button class="btn btn-vsm btn-primary pull-right" onclick="setCommunicationViewed(\'' + comId + '\');">Hide away</button>';
        $(slot).append(setViewedBtn);
    });
}