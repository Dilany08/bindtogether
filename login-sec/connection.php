<?php

function getDBConnection()
{

    $servername = "b2iaftd53m7qfeckgdcd-mysql.services.clever-cloud.com";
    $username = "ukmjtrrlicvc78dw";
    $password = "9Q2W9VCnKUc60s9eYzr6";
    $dbname = "b2iaftd53m7qfeckgdcd";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
