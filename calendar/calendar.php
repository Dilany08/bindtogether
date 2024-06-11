<?php
require_once '../login-sec/connection.php';
session_start();

?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Calendar</title>
<!-- CSS for FullCalendar -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css" rel="stylesheet" />
<!-- JS for jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<!-- JS for FullCalendar -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js"></script>
<!-- Bootstrap CSS and JS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"/>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
<link rel="stylesheet" href="../css/calendar.css">

</head>
<body>
<div class="container">
<a href="../Admin1/super_admin.php" class="btn btn-secondary back-button">
        <i class="fa-solid fa-arrow-left"></i> Back
    </a>

    <div class="row">
        <div class="col-lg-12">
        <h5 style="text-align:center;">Event Calendar</h5>
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Start popup dialog box -->
<div class="modal fade" id="event_entry_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Add/Edit Event</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="img-container">
                    <div class="row">
                        <div class="col-sm-12">
                            <input type="hidden" id="event_id">
                            <div class="form-group">
                              <label for="EventName">Event name</label>
                              <input type="text" name="EventName" id="EventName" class="form-control" placeholder="Enter your event name">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">  
                            <div class="form-group">
                              <label for="StartDate">Event start</label>
                              <input type="date" name="StartDate" id="StartDate" class="form-control" placeholder="Event start date">
                              <input type="time" name="StartTime" id="StartTime" class="form-control" placeholder="Event start time">
                             </div>
                        </div>
                        <div class="col-sm-6">  
                            <div class="form-group">
                              <label for="EndDate">Event end</label>
                              <input type="date" name="EndDate" id="EndDate" class="form-control" placeholder="Event end date">
                              <input type="time" name="EndTime" id="EndTime" class="form-control" placeholder="Event end time">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="save_event()">Save Event</button>
                <button type="button" class="btn btn-danger" onclick="delete_event()">Delete Event</button>
            </div>
        </div>
    </div>
</div>
<!-- End popup dialog box -->


<script>
$(document).ready(function() {
    display_events();
});

function display_events() {
    var events = new Array();
    $.ajax({
        url: 'display_event.php',
        dataType: 'json',
        success: function (response) {
            var result = response.data;
            $.each(result, function (i, item) {
                events.push({
                    EventID: result[i].EventID,
                    title: result[i].title + ' (' + moment(result[i].start).format('hh:mm A') + ' - ' + moment(result[i].end).format('hh:mm A') + ')',
                    start: result[i].start,
                    end: result[i].end,
                    color: result[i].color
                });  
            });

            $('#calendar').fullCalendar({
                defaultView: 'month',
                timeZone: 'local',
                editable: true,
                selectable: true,
                selectHelper: true,
                select: function(start, end) {
                    var now = moment().startOf('day');
                    if (start.isBefore(now)) {
                        alert('Cannot select a past date.');
                        $('#calendar').fullCalendar('unselect');
                        return;
                    }
                    $('#StartDate').val(moment(start).format('YYYY-MM-DD'));
                    $('#StartTime').val(moment(start).format('HH:mm'));
                    $('#EndDate').val(moment(end).format('YYYY-MM-DD'));
                    $('#EndTime').val(moment(end).format('HH:mm'));
                    $('#event_id').val('');
                    $('#EventName').val('');
                    $('#event_entry_modal').modal('show');
                },
                events: events,
                eventRender: function(event, element, view) { 
                    element.bind('click', function() {
                        $('#event_id').val(event.EventID);
                        $('#EventName').val(event.title);
                        $('#StartDate').val(moment(event.start).format('YYYY-MM-DD'));
                        $('#StartTime').val(moment(event.start).format('HH:mm'));
                        $('#EndDate').val(moment(event.end).format('YYYY-MM-DD'));
                        $('#EndTime').val(moment(event.end).format('HH:mm'));
                        $('#event_entry_modal').modal('show');
                    });
                }
            });
        },
        error: function (xhr, status, error) {
            alert('Error fetching events: ' + error);
        }
    });
}

function save_event() {
    var EventID = $("#event_id").val();
    var EventName = $("#EventName").val();
    var StartDate = $("#StartDate").val();
    var StartTime = $("#StartTime").val();
    var EndDate = $("#EndDate").val();
    var EndTime = $("#EndTime").val();

    var now = moment().startOf('day');
    var eventStart = moment(StartDate + ' ' + StartTime);
    var eventEnd = moment(EndDate + ' ' + EndTime);

    if (EventName == "" || StartDate == "" || StartTime == "" || EndDate == "" || EndTime == "") {
        alert("Please enter all required details.");
        return false;
    }

    if (eventStart.isBefore(now) || eventEnd.isBefore(now)) {
        alert("Cannot create or save an event in the past.");
        return false;
    }

    var url = EventID ? "update_event.php" : "save_event.php";

    $.ajax({
        url: url,
        type: "POST",
        dataType: 'json',
        data: {
            EventID: EventID,
            EventName: EventName,
            StartDate: StartDate,
            StartTime: StartTime,
            EndDate: EndDate,
            EndTime: EndTime
        },
        success: function(response) {
            $('#event_entry_modal').modal('hide');
            if (response.status == true) {
                alert(response.msg);
                location.reload();
            } else {
                alert(response.msg);
            }
        },
        error: function (xhr, status, error) {
            console.log('AJAX error: ' + error);
            alert('Error saving event: ' + error);
        }
    });

    return false;
}

function delete_event() {
    var EventID = $("#event_id").val();

    if (!EventID) {
        alert("No event selected.");
        return false;
    }

    if (!confirm("Are you sure you want to delete this event?")) {
        return false;
    }

    $.ajax({
        url: "delete_event.php",
        type: "POST",
        dataType: 'json',
        data: {
            EventID: EventID
        },
        success: function(response) {
            $('#event_entry_modal').modal('hide');
            if (response.status == true) {
                alert(response.msg);
                location.reload();
            } else {
                alert(response.msg);
            }
        },
        error: function (xhr, status, error) {
            console.log('AJAX error: ' + error);
            alert('Error deleting event: ' + error);
        }
    });

    return false;
}

function goBack() {
    window.location.href = "../superAdmin/super_admin.php"; // Replace with your specific page URL
}
</script>
</html>
