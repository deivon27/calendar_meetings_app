
$(document).ready(function () {
    /** Init Calendar Plugin ******/
    initCalendar();

    /** Init Colorpicker Bootstrap ******/
    initColorpicker();

    /** Init Datetimepicker Bootstrap ******/
    initDatetimePicker();

    /** Close Event Overlay Modal ******/
    $('.overlay-modal .close-thik').on('click', function () {
        $('.overlay-modal').fadeOut(500);
    });
});

/** Definition of useful variables ******/
var eventForm = $('#eventForm');
var token = eventForm.find("input[name=token]").val();
var root = "";

/**
 * Init Calendar Plugin function
 */
function initCalendar() {
    $('#mycalendar').html('');
    $('#mycalendar').monthly({
        mode: "event",
        //xmlUrl: 'vendor/monthly/events.xml',
        jsonUrl: root + "handlers/select_events.php",
        dataType: "json"
    });
}

/**
 * Init Datetimepicker Bootstrap function
 */
function initDatetimePicker() {
    eventForm.find('.datetimepicker').datetimepicker({
        format: 'YYYY-MM-DD HH:mm',
        sideBySide: true,
        //debug: true
    });
}

/**
 * Init Colorpicker function
 */
function initColorpicker() {
    eventForm.find('.colorpicker').colorpicker();
}

/**
 * Redirect with delay
 * @param page
 * @param delay
 */
function delayedRedirect(page, delay) {
    setTimeout(function () {
        window.location.href = page;
    }, parseInt(delay) * 1000);
}

/**
 * Execute some function with delay
 * @param action
 * @param delay
 */
function delayedAction(action, delay) {
    setTimeout(function () {
        action();
    }, parseInt(delay) * 1000);
}

/**
 * Response handler
 * @param elResp
 * @param html
 * @param type
 */
function response(elResp, html, type) {
    elResp.removeClass('reded greened greyed');
    if (type == 'error') {
        $(elResp).html(html).addClass('reded');
    } else {
        $(elResp).html(html).addClass('greened');
    }
    elResp.slideDown();
}

/**
 * Preloader handler
 * @param el
 */
function preloader(el) {
    el.addClass('greyed');
    el.html('Please wait...').slideDown();
}

/**
 * Open Event Overlay Modal
 * @param el
 * @param html
 * @param type
 */
function openModal(el, html, type) {
    $('.response').css('display', 'none');
    $('.overlay-modal').fadeIn(500);
    var form = $(el);
    form.find('h4 span.name-modal').html(html);
    $('.event-form').find('form').not(form).css('display', 'none');
    form.fadeIn();
    if (type == 1) form.find('.form-control').val('');
}

/**
 * Create new event
 * @returns {boolean}
 */
function createNewEvent() {
    var resp = eventForm.find('.response');
    preloader(resp);
    if (eventForm.find('input, textarea').val() != '') {
        $.ajax({
            type: "POST",
            url: root + "handlers/save_event.php",
            data: eventForm.serialize(),
            success: function (r) {
                if (r == 1) {
                    response(resp, 'Event was added successfully.', 'success');
                    function act() {$('.overlay-modal').fadeOut()}
                    delayedAction(act, 3);
                    initCalendar();
                } else {
                    response(resp, 'Some DB error was occured.', 'error');
                }
            }
        });
    }
    return false;
}

/**
 * Open for editing current event
 * @param id
 */
function openEditCurrEvent(id) {
    openModal('#eventForm', 'Edit the event', 0);
    eventForm.find('.submit-btn').attr('onclick', 'editCurrEvent(' + id + ')');
    $.ajax({
        type: "POST",
        url: root + "handlers/select_events.php",
        dataType: 'json',
        data: {id: id, token: token, typeSelect : 2},
        success: function (json) {
            for (var key in json['monthly'][0]) {
                var jsonNode = json['monthly'][0];
                var value = jsonNode[key];
                if (key == 'id' || key == 'status' || key == 'url') continue;
                if (key == 'startdate' || key == 'enddate' || key == 'starttime' || key == 'endtime') {
                    if (value && value != '') {
                        var startTime = (jsonNode.starttime).slice(0, -3);
                        var endTime = (jsonNode.endtime).slice(0, -3);

                        var dateStart = jsonNode.startdate + " " + startTime;
                        var dateEnd = jsonNode.enddate + " " + endTime;

                        eventForm.find('input[name=startdate]').val(dateStart);
                        eventForm.find('input[name=enddate]').val(dateEnd);
                        initDatetimePicker();
                    }
                } else if (key == 'description') {
                    eventForm.find('textarea[name=' + key + ']').val(value);
                } else {
                    eventForm.find('input[name=formAction]').val(2);
                    eventForm.find('input[name=' + key + ']').val(value);
                }
            }
        }
    });
}

/**
 * Edit and save current event
 * @param id
 */
function editCurrEvent(id) {
    var resp = eventForm.find('.response');
    preloader(resp);
    $.ajax({
        type: "POST",
        url: root + "handlers/save_event.php",
        data: eventForm.serialize() + '&id=' + id,
        dataType: 'json',
        success: function (r) {
            var eventDiv = $('.listed-event[data-eventid=' + id + ']'),
                color = r[0].color,
                name = r[0].name,
                description = r[0].description;

            eventDiv.css('backgroundColor', color);
            eventDiv.find('.title-text').html(name);
            eventDiv.find('.desc-event').html(description);

            var eventDivSmall = $('.monthly-event-indicator[data-eventid=' + id + ']')
                .not('.monthly-event-continued[data-eventid=' + id + ']');

            eventDivSmall.css('backgroundColor', color);
            eventDivSmall.find('span').html(name);
            $('.monthly-event-continued[data-eventid=' + id + ']').css({'backgroundColor' : color, color: color});


            if (r != 0) {
                response(resp, 'Event was edited successfully.', 'success');
                function act() {$('.overlay-modal').fadeOut()}
                delayedAction(act, 3);
            } else {
                response(resp, 'Some DB error was occured.', 'error');
            }
        }
    });
}

/**
 * Remove current event
 * @param id
 */
function removeCurrEvent(id) {
    $.ajax({
        type: "POST",
        url: root + "handlers/remove_event.php",
        data: {id: id, token: token},
        success: function (r) {
            if (r == 1) {
                var eventLink = $('.listed-event[data-eventid=' + id + '], ' +
                    '.monthly-event-indicator[data-eventid=' + id + ']');
                eventLink.parent('.monthly-list-item').removeClass('item-has-event').css('display', 'block');
                eventLink.slideUp(200, function () {
                    $(this).remove();
                });
            }
        }
    });
}

/**
 * Create new user
 * @returns {boolean}
 */
function inviteNewUser() {
    var form = $('#newUser');
    var resp = form.find('.response');
    if (form.find('input[type=email]').val()) {
        preloader(resp);
        $.ajax({
            type: "POST",
            url: root + "handlers/auth/invite_user.php",
            data: form.serialize(),
            success: function (r) {
                if (r == 1) {
                    response(resp, 'Invite link was sent successfully.', 'success');
                    function act() { $('.overlay-modal').fadeOut() }
                    delayedAction(act, 3);
                } else if (r == '') {
                    response(resp, 'Error of sending email. Verify SMTP settings.', 'error');
                } else {
                    response(resp, r, 'error');
                }
            }
        });
    }
    return false;
}