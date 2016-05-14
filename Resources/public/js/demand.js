/**
 * Demand left menu btns & slots
 * @type {{btn: string, slot: string}}
 */
var dm_dashboard = {
        btn: '#dm-dashboard',
        slot: '#sl-dashboard'
    },
    dm_new_project = {
        btn: '#dm-new-project',
        slot: '#sl-new-project'
    },
    dm_new_pipeline = {
        btn: '#dm-new-pipeline',
        slot: '#sl-new-pipeline'
    },
    dm_new_security = {
        btn: '#dm-new-security',
        slot: '#sl-new-security'
    };
/**
 * Current btn & slot
 */
var currentSlot = dm_dashboard;

/**
 * On document ready
 */
$(document).ready(function () {

    console.log('Demand JS Ready');

    // Dashboard demand handler
    $(dm_dashboard.btn).click(function () {
        btnAction(dm_dashboard);
    });

    // New project Demand handler
    $(dm_new_project.btn).click(function () {
        btnAction(dm_new_project);
    });

    // New pipeline Demand handler
    $(dm_new_pipeline.btn).click(function () {
        btnAction(dm_new_pipeline);
    });

    // Security improvment Demand handler
    $(dm_new_security.btn).click(function () {
        btnAction(dm_new_security);
    });

    // Analize URL
    analizeUrl();

});

/**
 * Function called from btn click
 * @param obj
 */
function btnAction(obj) {
    if ($(obj.slot).hasClass('hidden')) {
        // Hide current slot
        toggleCase(currentSlot, "primary", "hidden");
        // Show new slot
        toggleCase(obj, "success", "show");
        // Set new slot
        currentSlot = obj;
    }
}

/**
 * Hydrate toggleSlot and toggleBtn with passed values
 * @param obj
 * @param btnTo
 * @param slotTo
 */
function toggleCase(obj, btnTo, slotTo) {
    if (typeof obj !== "undefined") {
        toggleBtn(obj, btnTo);
        toggleSlot(obj, slotTo);
    }
}

/**
 * Toggle a slot from hidden to show or from show to hidden
 * @param obj
 * @param to
 */
function toggleSlot(obj, to) {
    if (to === "hidden") {
        $(obj.slot).addClass("hidden");
    }
    if (to === "show") {
        $(obj.slot).removeClass("hidden");
    }
}

/**
 * Toggles the btn from primary to success or from success to primary
 * @param obj
 * @param to
 */
function toggleBtn(obj, to) {
    if (to === "primary") {
        $(obj.btn).removeClass('btn-success').addClass('btn-primary');
    }
    if (to === "success") {
        $(obj.btn).removeClass('btn-primary').addClass('btn-success');
    }
}

function vcsNewProjectChange(obj) {
    var value = obj.value;
    var cbList = ['demand_new_project_gitLabIssueEnabled',
        'demand_new_project_gitLabWikiEnabled',
        'demand_new_project_gitLabSnippetsEnabled'];
    if (value === 'yes') {
        $('#collapseVcs').addClass('in');
        $('#collapsePm').removeClass('in');
        $('#collapseCi').removeClass('in');
        checkCheckbox(cbList, true);
    }
    if (value === 'no') {
        checkCheckbox(cbList, false);
    }
}
function pmNewProjectChange(obj) {
    var value = obj.value;
    if (value === 'yes') {
        $('#collapsePm').addClass('in');
        $('#collapseVcs').removeClass('in');
        $('#collapseCi').removeClass('in');
    }
    if (value === 'no') {

    }
}
function ciNewProjectChange(obj) {
    var value = obj.value;
    var cbList = ['demand_new_project_qaDevManaged', 'demand_new_project_ciDevManaged'];
    if (value === 'yes') {
        $('#collapseCi').addClass('in');
        $('#collapseVcs').removeClass('in');
        $('#collapsePm').removeClass('in');
        checkCheckbox(cbList, true);
    }
    if (value === 'no') {
        checkCheckbox(cbList, false);
    }
}

function checkCheckbox(cbList, checkIt) {
    $.each(cbList, function (i, v) {
        $('#' + v).prop('checked', checkIt);
    })
}

function securityAnalysisChange(obj) {
    console.log('Has changed');
    var value = obj.value;
    if (value === 'static') {
        $('#serverTaget').hide();
    } else if (value === 'dynamic') {
        $('#serverTaget').show();
    }
}

/**
 * Function analizing url and switching to slot depending on
 */
function analizeUrl() {
    // Get current anchor
    var anchorValue = getAnchor();
    if (anchorValue !== null) {
        // If Dashboard anchor
        if (anchorValue === "dashboard") {
            $(dm_dashboard.btn).click();
        }
        // If New Project anchor
        if (anchorValue === "new_project") {
            $(dm_new_project.btn).click();
        }
        // If New Pipeline anchor
        if (anchorValue === "new_pipeline") {
            $(dm_new_pipeline.btn).click();
        }
        // If Security improvment anchor
        if (anchorValue === "new_security") {
            $(dm_new_security.btn).click();
        }
    }
}

/**
 * Return current url anchor value
 * @returns {null}
 */
function getAnchor() {
    return (document.URL.split('#').length > 1) ? document.URL.split('#')[1] : null;
}