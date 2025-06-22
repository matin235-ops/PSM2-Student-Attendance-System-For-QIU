<?php
$conn = mysqli_connect("localhost","root","","attendancemsystem");

if(!$conn){
    die("Connection failed: " . mysqli_connect_error());
}