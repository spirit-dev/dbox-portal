$(document).ready(function () {

    console.log('Admin side ready !');


    //setTimeout(manageNewComForm, 1500);
    setTimeout(datePick, 1500);

});

function manageNewComForm() {

    var newComForm = '#new-communication-form';

    if ($(newComForm).length) {

        var showFrom = '#communication_showFromDate';
        var showTo = '#communication_showToDate';

        var dateNow = new Date();
        var date1W = new Date();
        date1W.setDate(date1W.getDate() + 7);

        var showFromCB = $(showFrom).parent().find('.nullable-control').find('label').find('input');
        if ($(showFromCB).is(':checked')) {
            $(showFromCB).prop('checked', false);
            $(showFrom).css("display", "block");
            $.each($(showFrom).find('select'), function (key, val) {
                $(val).removeAttr('disabled');
                if (key === 0) $(val).val(dateNow.getFullYear());
                if (key === 1) $(val).val((dateNow.getMonth() + 1));
                if (key === 2) $(val).val(dateNow.getDate());
            });
        }

        var showToCB = $(showTo).parent().find('.nullable-control').find('label').find('input');
        if ($(showToCB).is(':checked')) {
            $(showToCB).prop('checked', false);
            $(showTo).css("display", "block");
            $.each($(showTo).find('select'), function (key, val) {
                $(val).removeAttr('disabled');
                if (key === 0) $(val).val(date1W.getFullYear());
                if (key === 1) $(val).val((date1W.getMonth() + 1));
                if (key === 2) $(val).val(date1W.getDate());
            });
        }
    }
}

function datePick() {

    var newComForm = '#new-communication-form';

    if ($(newComForm).length) {
        var showFrom = '#communication_showFromDate';
        var showTo = '#communication_showToDate';

        var showFromCB = $(showFrom).parent().find('.nullable-control').find('label').find('input');
        if ($(showFromCB).is(':checked')) $(showFromCB).prop('checked', false);
        var showToCB = $(showTo).parent().find('.nullable-control').find('label').find('input');

        if ($(showToCB).is(':checked')) $(showToCB).prop('checked', false);

        var dateFormat = "yy-mm-dd";
        $(showFrom).datepicker({
            dateFormat: dateFormat
        });
        $(showTo).datepicker({
            dateFormat: dateFormat
        });
    }

}