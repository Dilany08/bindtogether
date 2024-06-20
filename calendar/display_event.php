<?php
require_once '../login-sec/connection.php';
session_start();

$conn = getDBConnection();

function generateLightColor()
{
    $r = rand(127, 255);
    $g = rand(127, 255);
    $b = rand(127, 255);
    return sprintf("#%02x%02x%02x", $r, $g, $b);
}

$display_query = "SELECT EventID, EventName, StartDate, StartTime, EndDate, EndTime FROM calendar";
$results = mysqli_query($conn, $display_query);
$count = mysqli_num_rows($results);

if ($count > 0) {
    $data_arr = array();
    $i = 1;

    while ($data_row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
        $data_arr[$i]['EventID'] = $data_row['EventID'];
        $data_arr[$i]['title'] = $data_row['EventName'];
        $data_arr[$i]['start'] = $data_row['StartDate'] . 'T' . $data_row['StartTime'];
        $data_arr[$i]['end'] = $data_row['EndDate'] . 'T' . $data_row['EndTime'];
        $data_arr[$i]['color'] = generateLightColor();
        $i++;
    }

    $data = array(
        'status' => true,
        'msg' => 'successfully!',
        'data' => $data_arr
    );
} else {
    $data = array(
        'status' => false,
        'msg' => 'Error!'
    );
}

echo json_encode($data);

$conn->close();
