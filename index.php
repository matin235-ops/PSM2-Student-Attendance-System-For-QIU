<?php 
include 'Includes/dbcon.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="../QIULOGO1.png" rel="icon">
    <title>QIU Attendance System - Login</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
    <style>
        /* Video Background */
        #background-video {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }

        /* Overlay */
        .video-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        /* Animation for login box */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 60px;
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 470px;
            animation: fadeIn 1s ease-out;
            position: relative;
            z-index: 1;
        }

        .login-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .login-header img {
            width: 150px;
            height: auto;
            margin-bottom: 15px;
        }

        .login-header h1 {
            font-size: 24px;
            color: #333;
        }

        .form-control {
            border-radius: 5px;
            border: 1px solid #ddd;
            box-shadow: none;
            margin-bottom: 20px;
        }

        .btn-success {
            background-color: #28A744FF;
            border-color: #28A74AFF;
            font-size: 16px;
        }

        .btn-success:hover {
            background-color: #2F8821FF;
        }

        .footer-text {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color:#333;
            position: relative;
            z-index: 1;
        }
    </style>
</head>

<body>
    <video autoplay loop muted playsinline id="background-video">
        <source src="video.mp4" type="video/mp4">
    </video>
    <div class="video-overlay"></div>
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="login-container">
            <div class="login-header">
                <img src="./QIU.png" alt="QIU Logo">
                <h1 class="h4 text-gray-900 mb-4">QIU ATTENDANCE SYSTEM</h1>
            </div>
            <h5 class="text-center">Login Panel</h5>
            <form class="user" method="POST" action="">
                <div class="form-group">
                    <select required name="userType" class="form-control">
                        <option value="">--Select User Role--</option>
                        <option value="Administrator">Administrator</option>
                        <option value="ClassTeacher">Class Teacher</option>
                    </select>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" required name="username" placeholder="Enter Email Address">
                </div>
                <div class="form-group">
                    <input type="password" name="password" required class="form-control" placeholder="Enter Password">
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-success btn-block" value="Login" name="login" />
                </div>
            </form>
            <?php
            if (isset($_POST['login'])) {
                $userType = $_POST['userType'];
                $username = $_POST['username'];
                $password = md5($_POST['password']);
                
                $table = ($userType == "Administrator") ? "tbladmin" : "tblclassteacher";
                $query = "SELECT * FROM $table WHERE emailAddress = ? AND password = ?";
                
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ss", $username, $password);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $_SESSION['userId'] = $row['Id'];
                    $_SESSION['firstName'] = $row['firstName'];
                    $_SESSION['lastName'] = $row['lastName'];
                    $_SESSION['emailAddress'] = $row['emailAddress'];
                    if ($userType == "ClassTeacher") {
                        $_SESSION['classId'] = $row['classId'];
                        $_SESSION['classArmId'] = $row['classArmId'];
                    }
                    $redirectPage = ($userType == "Administrator") ? "Admin/index.php" : "ClassTeacher/index.php";
                    echo "<script>window.location.href = '$redirectPage';</script>";
                } else {
                    echo "<div class='alert alert-danger' role='alert'>Invalid Username/Password!</div>";
                }
                $stmt->close();
            }
            ?>
            <div class="footer-text">
                <p>&copy; <script>document.write(new Date().getFullYear());</script> QIU Attendance System | Developed by Matin Khaled</p>
            </div>
        </div>
    </div>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/ruang-admin.min.js"></script>
    <script>
        const video = document.getElementById('background-video');
        video.onended = () => {
            video.play();
        };
    </script>
</body>
</html>
