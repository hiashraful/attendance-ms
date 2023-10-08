<?php
session_start();
include "connect.php";

if (isset($_POST['u_email']) && isset($_POST['u_password'])) {

    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $email = validate($_POST['u_email']);
    $password = validate($_POST['u_password']);

    if (empty($email)) {
        header("Location: index.php?error=User Name is required");
        exit();
    } else if (empty($password)) {
        header("Location: index.php?error=Password is required");
        exit();
    } else {
        $sql = "SELECT * FROM emp WHERE email=? AND password=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email, $password]);

        if ($stmt->rowCount() === 1) {
            $row = $stmt->fetch();
            if ($row['email'] === $email && $row['password'] === $password) {
                $_SESSION['user_id'] = $row['id']; 
                $_SESSION['email'] = $row['email'];
                $_SESSION['username'] = $row['name'];

                $loginDate = date("Y-m-d"); 
                $loginTime = date("H:i:s");
                $sql = "INSERT INTO emp_history (user_id, login_date, login_time) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$_SESSION['user_id'], $loginDate, $loginTime]);

                header("Location: dashboard.php");
                exit();
            } else {
                header("Location: index.php?error=Incorrect User name or password");
                exit();
            }
        } else {
            header("Location: index.php?error=Incorrect User name or password");
            exit();
        }
    }

} else {
    header("Location: index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loggin in...</title>
</head>
<body>
    
    <!-- ====================== Location Script ====================== -->
    <script>
        if ("geolocation" in navigator) {
    navigator.geolocation.getCurrentPosition(function (position) {
        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;

        // Send the coordinates and user's IP address to a PHP script
        var userIP = '<?php echo $_SERVER['REMOTE_ADDR']; ?>'; // Get the user's IP
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "login.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Handle the response from the server if needed
                console.log(xhr.responseText);
            }
        };
        xhr.send("latitude=" + latitude + "&longitude=" + longitude + "&userIP=" + userIP);
    });
}
    </script>
</body>
</html>