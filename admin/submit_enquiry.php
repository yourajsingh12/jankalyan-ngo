<?php
include('db_conn.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $range = mysqli_real_escape_string($conn, $_POST['range']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);

    $query = "INSERT INTO enquiries (name, phone, range_option, location)
              VALUES ('$name', '$phone', '$range', '$location')";

    if (mysqli_query($conn, $query)) {
        echo "success"; 
    } else {
        echo "error";
    }
}
?>
