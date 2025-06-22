<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

if(isset($_POST['search'])) {
    $search = mysqli_real_escape_string($conn, $_POST['search']);
    
    $query = "SELECT s.*, c.className 
              FROM tblstudents s
              LEFT JOIN tblclass c ON s.classId = c.Id
              WHERE s.firstName LIKE '%$search%' 
              OR s.lastName LIKE '%$search%'
              OR s.regNumber LIKE '%$search%'
              OR c.className LIKE '%$search%'
              LIMIT 8";
              
    $result = $conn->query($query);
    
    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<div class='p-2 border-bottom search-item'>";
            echo "<strong>" . htmlspecialchars($row['firstName'] . ' ' . $row['lastName']) . "</strong><br>";
            echo "<small>Reg No: " . htmlspecialchars($row['regNumber']) . "</small><br>";
            echo "<small>Class: " . htmlspecialchars($row['className']) . "</small>";
            echo "</div>";
        }
    } else {
        echo "<div class='p-2'>No results found</div>";
    }
}