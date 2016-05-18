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
 * File           project.js
 * Updated the    18/05/16 18:07
 */

/**
 * Created by JuanitoP on 29/10/2015.
 */

$(document).ready(function () {
    // Called on each page load, reset cloning url, fixed to html
    resetCloneUrl();

    // Init CopyToClipboard feature
    initCTC();

    // Bind setting settings button
    initBtnSettings();

});

/**
 * Called on cloning url, change url datas
 * @param switchTo
 */
function switchCloneUrl(switchTo) {

    var btn_ssh = '#btn-clone-uri-ssh';
    var btn_http = '#btn-clone-uri-http';
    var input_clone_url = '#clone-url';

    if (switchTo === 'http') {
        var httpUrl = $('#http-url').val();
        $(btn_ssh).removeClass('active');
        $(btn_http).addClass('active');
        $(input_clone_url).val(httpUrl);
        $('#copy-clone-url').attr('data-clipboard-text', httpUrl);
    }
    if (switchTo === 'ssh') {
        var sshUrl = $('#ssh-url').val();
        $(btn_http).removeClass('active');
        $(btn_ssh).addClass('active');
        $(input_clone_url).val(sshUrl);
        $('#copy-clone-url').attr('data-clipboard-text', sshUrl);
    }
}

/**
 * Called on each page load, reset cloning url, fixed to html
 */
function resetCloneUrl() {
    var url = $('#http-url').val();
    $('#clone-url').val(url);
}

/**
 * Init CopyToClipboard feature
 */
function initCTC() {
    ZeroClipboard.config({swfPath: 'http://portal.devtest.fr/bundles/spiritdevdboxportal/lib/zeroclipboard220/dist/ZeroClipboard.swf'});
    new ZeroClipboard(document.getElementById('copy-clone-url'));
}

/**
 * Calles on url switching, change datas
 * @param switchTo
 */
function switchGotoUrl(switchTo) {

    var goto_url = '#goto_url';
    if (switchTo === 'gitlab') {
        $(goto_url).val($('#gitlab_hurl').val());
    }
    if (switchTo === 'redmine') {
        $(goto_url).val($('#redmine_hurl').val());
    }
    if (switchTo === "jenkins") {
        $(goto_url).val($('#jenkins_hurl').val());
    }
    if (switchTo === "sonar") {
        $(goto_url).val($('#sonar_hurl').val());
    }

}

/**
 * Bind setting settings button
 */
function initBtnSettings() {

    var slot = '#pjt-settings';

    $('#btn-pjt-config').click(function () {
        if ($(slot).is(':visible')) {
            $(slot).hide('400');
        }
        else {
            $(slot).show('400');
        }
    });

}

/**
 * Setting - add user to project
 * @param projectId
 */
function addUserToProject(projectId) {
    var userId = $('#add-user').val();
    bootbox.dialog({
        size: 'small',
        message: "Are you sure you want to add this user from project ?",
        buttons: {
            success: {
                label: "Cancel",
                className: "btn-default",
                callback: function () {
                }
            },
            danger: {
                label: "OK",
                className: "btn-success",
                callback: function () {
                    post('/project_add_user', {
                        'userId': userId,
                        'projectId': projectId
                    });
                }
            }
        }
    });
}

/**
 * Settings - remove user from project
 * @param projectId
 * @param userId
 */
function removeUserFromProject(projectId, userId) {
    bootbox.dialog({
        size: 'small',
        message: "Are you sure you want to delete this user from project ?",
        buttons: {
            success: {
                label: "Cancel",
                className: "btn-default",
                callback: function () {
                }
            },
            danger: {
                label: "OK",
                className: "btn-danger",
                callback: function () {
                    post('/project_rm_user', {
                        'userId': userId,
                        'projectId': projectId
                    });
                }
            }
        }
    });
}

/**
 * This function is the entry point to launch an ajax job
 * It switches depending on parametrized job
 * @param ciId
 * @param parameters
 */
function launchJob(ciId, parameters) {

    var hasParams = false;

    if (typeof parameters !== 'undefined') {
        hasParams = true;
        parameters = JSON.parse(parameters);
    }

    if (!hasParams) {
        launchNoParamsJob(ciId);
    } else if (hasParams) {
        launchParamsJob(ciId, parameters);
    }

}

/**
 * This function defines the parameters to set
 * @param parameters
 * @returns {Array}
 */
function defineParametersToSet(parameters) {
    var paramsToSet = [];
    for (i = 0; i < parameters.length; i++) {
        var param = parameters[i];
        if (!param['hide']) {
            paramsToSet.push(param);
        }
    }
    return paramsToSet;
}

/**
 * Ask user to go and launches the request
 * @param ciId
 */
function launchNoParamsJob(ciId) {

    bootbox.dialog({
        size: 'small',
        message: "Do you want to launch this job ?",
        buttons: {
            success: {
                label: "Cancel",
                className: "btn-default",
                callback: function () {
                }
            },
            danger: {
                label: "OK",
                className: "btn-success",
                callback: function () {
                    launchJobRequest(ciId, null);
                }
            }
        }
    });
}

/**
 * Ask user to go and launches a parametrized job
 * It switches asks between hidden values and not hidden ones
 * @param ciId
 * @param parameters
 */
function launchParamsJob(ciId, parameters) {

    var paramsToSet = defineParametersToSet(parameters);

    if (paramsToSet.length > 0) {

        var subContent = '';
        for (i = 0; i < paramsToSet.length; i++) {
            var param = paramsToSet[i];
            subContent +=
                '<div class="form-group">' +
                '<label class="col-md-4 control-label" for="' + param['name'] + '">' + param['name'] + '</label>' +
                '<div class="col-md-8">' +
                '<input id="' + param['name'] + '" name="' + param['name'] + '" type="text" placeholder="' + param['description'] + '" class="form-control input-md" value="' + param['default'] + '">' +
                '</div>' +
                '</div>';
        }

        var htmlContent = '<div class="row">' +
            '<div class="col-md-12">' +
            '<form class="form-horizontal">' +
            subContent +
            '</form>' +
            '</div>' +
            '</div>';

        bootbox.dialog({
                title: "This job has some parameters to be defined.",
                message: htmlContent,
                buttons: {
                    success: {
                        label: "Launch job",
                        className: "btn-success",
                        callback: function () {
                            var paramsToTransmit = [];
                            for (i = 0; i < paramsToSet.length; i++) {
                                paramsToTransmit[paramsToSet[i]['name']] = $('#' + paramsToSet[i]['name']).val();
                            }
                            launchJobRequest(ciId, JSON.stringify(paramsToTransmit));
                        }
                    },
                    cancel: {
                        label: "Cancel",
                        className: "btn-default",
                        callback: function () {
                        }
                    }
                }
            }
        );
    } else {

        bootbox.dialog({
            size: 'small',
            message: "Do you want to launch this job ?",
            buttons: {
                success: {
                    label: "Cancel",
                    className: "btn-default",
                    callback: function () {
                    }
                },
                danger: {
                    label: "OK",
                    className: "btn-success",
                    callback: function () {
                        var paramsToTransmit = [];
                        for (i = 0; i < parameters.length; i++) {
                            paramsToTransmit[parameters[i]['name']] = parameters[i]['default'];
                        }
                        launchJobRequest(ciId, JSON.stringify(paramsToTransmit));
                    }
                }
            }
        });
    }
}

/**
 * Launches the request
 * @param ciId
 * @param parametersToTransmit
 */
function launchJobRequest(ciId, parametersToTransmit) {

    showLoader();
    $.ajax({
        type: 'POST',
        url: '/project_launch_ci_job',
        data: {
            ciId: ciId,
            parameters: parametersToTransmit
        }
    }).done(function (data) {
        if (data) {
            displayProgressBar(ciId);
            reachProgression(ciId);
        }
    }).fail(function (data) {
        showCommunication('Job launch failure', 'Sorry the job you\'ve launch have failed! You may warn your administrator!', 'error');
    }).always(function () {
        hideLoader();
    });
}

/**
 * Function displaying the progress bar
 */
function displayProgressBar(ciId) {
    $('#slot-ci-launch-job-' + ciId).hide();
    $('#slot-progress-bar-' + ciId).removeClass('col-sm-11').addClass('col-sm-12');
    var progressBar = '<div class="progress" id="pb">' +
        '<div id="pb-pb" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0"' +
        'aria-valuemax="100" style="width: 0%;">0%' +
        '</div>' +
        '</div>';
    $('#pb-content').append(progressBar);
}

/**
 * Function updating the progress bar
 * @param percent
 */
function updateProgressBar(percent) {
    var pb = $('#pb-pb');
    if (percent === 100) {
        pb.addClass('progress-bar-success');
    } else {
        pb.removeClass('progress-bar-success');
    }
    pb.attr('aria-valuenow', percent);
    pb.attr('style', 'width: ' + percent + '%;');
    pb.html(percent + '%');
}

/**
 * Function removing the progress bar
 */
function removeProgressBar() {
    $('#pb').remove();
}

/**
 * Function reaching the ci Progression
 * @param ciId
 */
function reachProgression(ciId) {

    if (!!window.EventSource) {
        // Creating socket
        var source = new EventSource('/project_ci_job_progress_sse?ciId=' + ciId);

        // Initializing some vars
        var previousVal = 0;

        // Listening for server side event
        source.addEventListener('progress', function (e) {
            if (e.origin != 'http://portal.devtest.fr') {
                alert('Origin was not http://portal.devtest.fr - Closing Socket');
                source.close();
                return;
            }
            console.log(e);
            var obj = JSON.parse(e.data);

            if (parseInt(obj.progress) === 0 && parseInt(obj.progress) < previousVal) {
                updateProgressBar(100);
                console.log('SSE closing connection');
                source.close();
                // removeProgressBar();
                showCommunication('Job completed', 'The job you\'ve launched have been finished.<br/>The page will be reloaded', 'success');
                reloadPage(1000);
            }
            else if (parseInt(obj.progress)) {
                updateProgressBar(obj.progress);
                previousVal = obj.progress;
            }
        }, false);

        source.addEventListener('error', function (e) {
            if (e.readyState == EventSource.CLOSED) {
                console.log('SSE connection closed');
            }
        }, false);
    } else {
        console.log('No SSE, using old ajax poll');

        var previousVal = 'null';
        var userPrevented = false;

        setTimeout(function () {
            var timeouted = setInterval(function () {
                showLoader();
                $.ajax({
                    type: 'POST',
                    url: '/project_ci_job_progress',
                    data: {
                        ciId: ciId
                    }
                }).done(function (data) {
                    if (parseInt(data)) {
                        updateProgressBar(data);
                        previousVal = data;
                    } else if (data === 'null') {
                        if (parseInt(previousVal)) {
                            window.clearInterval(timeouted);
                            removeProgressBar();
                            if (!userPrevented) {
                                showCommunication('Job completed', 'The job you\'ve launched have been finished.<br/>The page will be reloaded', 'success');
                                userPrevented = true;
                            }
                            reloadPage();
                        }
                    }
                }).always(function () {
                    hideLoader();
                });
            }, 2000);

            setTimeout(function () {
                if (previousVal === 'null') {
                    window.clearInterval(timeouted);
                    removeProgressBar();
                    reloadPage();
                }
            }, 20000);

        }, 1000);
    }

}

/**
 * Function called via project settings
 * @param item
 * @param pjtId
 */
function createItem(item, pjtId) {
    bootbox.dialog({
        size: 'small',
        message: "Are you sure you want to create " + item.toUpperCase() + " for this project",
        buttons: {
            success: {
                label: "Cancel",
                className: "btn-default",
                callback: function () {
                }
            },
            danger: {
                label: "OK",
                className: "btn-danger",
                callback: function () {
                    showLoader();
                    showLargeLoader();
                    $.ajax({
                        url: "/project_add_manager",
                        method: 'POST',
                        data: {
                            'manager': item,
                            'pjt_id': pjtId
                        }
                    }).done(function (data) {
                        console.log(data);
                        showCommunication(item.toUpperCase() + ' creation OK', item.toUpperCase() + ' Creation for this project well created! The page will be reloaded', 'success');
                        reloadPage(1500);
                    }).fail(function (data) {
                        console.log(data);
                        showCustomCommunication({
                            'title': item.toUpperCase() + ' creation ERROR',
                            'text': item.toUpperCase() + ' Creation for this project Failed!\n\nError : ' + data.responseJSON + '\n\nYou may contact your administrator!',
                            'type': 'error',
                            'hide': false
                        });
                    }).always(function () {
                        hideLoader();
                        hideLargeLoader();
                    });
                }
            }
        }
    });
}