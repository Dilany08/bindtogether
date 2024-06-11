<?php
require_once '../login-sec/connection.php';
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>User Calendar</title>
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

<style>
.container {
    padding: 3rem;
}
.back-button {
    position: fixed;
    top: 40px;
    left: 40px;
    background-color: #7D0A0A;
}

.back-button:hover {
    background-color: #c72d2d;
}

/* New CSS to make calendar lines black */
.fc td, .fc th {
    border-color: black !important;
}

.fc-day-grid-event, .fc-time-grid-event {
    border: 1px solid black !important;
}
</style>

</head>
<body>
<div class="container">
    <button class="btn btn-secondary back-button" onclick="goBack()">
        <i class="fa-solid fa-arrow-left"></i> Back
    </button>

    <div class="row">
        <div class="col-lg-12">
        <h3 style="text-align:center;">Event Calendar</h3>
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Start popup dialog box -->
<div class="modal fade" id="event_entry_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Event Details</h5>
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
                              <input type="text" name="EventName" id="EventName" class="form-control" placeholder="Enter your event name" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">  
                            <div class="form-group">
                              <label for="StartDate">Event start</label>
                              <input type="date" name="StartDate" id="StartDate" class="form-control" readonly>
                              <input type="time" name="StartTime" id="StartTime" class="form-control" readonly>
                             </div>
                        </div>
                        <div class="col-sm-6">  
                            <div class="form-group">
                              <label for="EndDate">Event end</label>
                              <input type="date" name="EndDate" id="EndDate" class="form-control" readonly>
                              <input type="time" name="EndTime" id="EndTime" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                              <label for="Email">Enter your Email to get a reminder:</label>
                              <input type="Email" name="Email" id="Email" class="form-control" placeholder="Enter your Email">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="save_reminder()">Save Reminder</button>
            </div>
        </div>
    </div>
</div>
<!-- End popup dialog box -->

<br>

</body>

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
                editable: false,
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

function save_reminder() {
    var EventID = $("#event_id").val();
    var Email = $("#Email").val();

    if (Email == "") {
        alert("Please enter your Email.");
        return false;
    }

    $.ajax({
        url: "save_reminder.php",
        type: "POST",
        dataType: 'json',
        data: {
            EventID: EventID,
            Email: Email
        },
        success: function(response) {
            $('#event_entry_modal').modal('hide');
            if (response.status == true) {
                alert(response.msg);
            } else {
                alert(response.msg);
            }
        },
        error: function (xhr, status, error) {
            console.log('AJAX error: ' + error);
            alert('Error saving reminder: ' + error);
        }
    });

    return false;
}

function goBack() {
    window.location.href = "../admin/dashboard.php"; // Replace with your specific page URL
}
</script>
</html>
