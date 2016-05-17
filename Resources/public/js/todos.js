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
 * File           todos.js
 * Updated the    17/05/16 21:18
 */

$(document).ready(function () {

    $('#sendTodos').click(function () {
        sendTodos();
    });

    $('#checkAll').click(function () {
        checkAll();
    });

    $('#uncheckAll').click(function () {
        uncheckAll();
    });

});

function todocheck(tr) {

    var jqTr = $(tr);
    var span = jqTr.find('.content-td').find('.glyphicon');
    console.log(span);

    if (jqTr.hasClass('todo-checked')) {
        jqTr.removeClass('todo-checked');

        span.removeClass('glyphicon-ok');
        span.addClass('glyphicon-arrow-right');
    } else {
        jqTr.addClass('todo-checked');

        span.removeClass('glyphicon-arrow-right');
        span.addClass('glyphicon-ok');
    }
}

function checkAll() {
    var table = $('#todo-tbl');

    $.each(table.find('tr'), function (i, val) {

        if (!$(this).hasClass('todo-checked')) {
            $(this).addClass('todo-checked');

            var span = $(this).find('.content-td').find('.glyphicon');
            span.removeClass('glyphicon-arrow-right');
            span.addClass('glyphicon-ok');
        }

    });
}

function uncheckAll() {
    var table = $('#todo-tbl');

    $.each(table.find('tr'), function (i, val) {
        $(this).removeClass('todo-checked');

        var span = $(this).find('.content-td').find('.glyphicon');
        span.removeClass('glyphicon-ok');
        span.addClass('glyphicon-arrow-right');
    });
}

function sendTodos() {

    var table = $('#todo-tbl');

    var todoRemove = [];

    $.each(table.find('tr'), function (i, val) {
        var jqTr = $(this);
        if (jqTr.hasClass('todo-checked')) {
            var tdId = jqTr.find('.hidden-td').html();
            todoRemove.push(tdId);
        }
    });

    bootbox.dialog({
        size: 'small',
        message: "Are you sure you want to delete theses todos ?",
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
                    post('/todos/remove', {
                        'todos': JSON.stringify(todoRemove)
                    });
                }
            }
        }
    });
}