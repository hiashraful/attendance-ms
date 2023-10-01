<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// Retrieve the username from the session
$username = $_SESSION['username'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <p id="login-success">Logged In Successfully!</p>
    <form action="" method="post">
        <div class="dashboard-container">
            <h1 id="dashboard-heading">Dashboard</h1>
            <p>Welcome, <?php echo $username; ?> </p>
        </div>
    </form>
    <div class="logout">
        <button id="btn-logout"><a href="logout.php">Logout</a></button>
    </div>
    <script>
        setTimeout(function() {
            var loginMessage = document.getElementById('login-success');
            if (loginMessage) {
                loginMessage.style.display = 'none';
            }
        }, 1500);
    </script>
</body>
</html>