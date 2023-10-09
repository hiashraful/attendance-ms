<?php
session_start();
include "connect.php";
date_default_timezone_set('Asia/Dhaka');
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
                $_SESSION['role'] = $row['role'];

                // Get user's IP address
                $userIP = $_SERVER['REMOTE_ADDR'];

                // Use an IP geolocation API to get city name based on the user's IP address
                $ipInfo = json_decode(file_get_contents("http://ipinfo.io/{$userIP}/json"));
                $cityName = $ipInfo->city;

                // Retrieve and store latitude and longitude from the client-side
                $latitude = $_POST['latitude'];
                $longitude = $_POST['longitude'];
                $latitude = (float) $latitude;
                $longitude = (float) $longitude;

                // Insert user location data into the database
                $loginDate = date("Y-m-d");
                $loginTime = date("H:i:s");
                $sql = "INSERT INTO emp_history (user_id, login_date, login_time, user_ip, city_name, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$_SESSION['user_id'], $loginDate, $loginTime, $userIP, $cityName, $latitude, $longitude]);

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
