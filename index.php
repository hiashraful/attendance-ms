<?php
session_start();
if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    $logoutMessage = "Log out successful!";
}

if (isset($_SESSION['email'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management System</title>
    <link rel="stylesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <form action="login.php" method="post">
        <div class="login-container">
            <?php if (isset($_GET['error'])) {?>
                <p id="error"><?php echo $_GET['error']; ?></p>
            <?php }?>
            <?php if (isset($logoutMessage)) {?>
                <p id="logout-msg"><?php echo $logoutMessage; ?></p>
            <?php }?>
            <h1 id="login-heading">ATTENDANCE MANAGEMENT SYSTEM</h1>
            <div class="icon">
                <img src="img/user.png" width="60px">
            </div>
            <p>Login Panel</p>
            <div class="input-box">
                <input type="email" name="u_email" id="email" placeholder="Enter email address" required>
                <i class='bx bx-user'></i>
            </div>
            <div class="input-box">
                <input type="password" name="u_password" id="password" placeholder="Enter password" required>
                <i class='bx bxs-lock'></i>
            </div>
            <!-- Location data -->
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            <input type="submit" name="submit" value="Log In" id="login-btn">
        </div>
    </form>
    <! -- ====================== Location Script ====================== -->
    <script>
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(function (position) {
            var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;
            document.getElementsByName("latitude")[0].value = latitude;
            document.getElementsByName("longitude")[0].value = longitude;
            });
        } else {
            console.log("Browser doesn't support geolocation!");
        }
    </script>
    <script>
        setTimeout(function() {
            var errorMessage = document.getElementById('error');
            var logoutMessage = document.getElementById('logout-msg');
            if (errorMessage) {
                errorMessage.style.display = 'none';
            }
            if (logoutMessage) {
                logoutMessage.style.display = 'none';
            }
        }, 2000);
    </script>
    <!-- ====================== Location Script ====================== -->
    <script>
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(function (position) {
            var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;

            $.ajax({
                type : "POST",  //type of method
                url  : "location.php",  //your page
                data : { latitude : latitude, longitude : longitude},// passing the values
                success: function(res){
                                        console.log("success");
                        }
            });
        });
    }
    </script>
</body>
</html>