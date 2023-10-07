<?php
session_start();

require_once "connect.php";

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// updated hours calculation
$userId = $_SESSION['user_id'];
$logoutDate = date("Y-m-d");
$logoutTime = date("H:i:s");

// Check if there's an existing record for this user on the same date
$sql = "SELECT * FROM user_history WHERE user_id = ? AND login_date = ? ORDER BY login_time DESC LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId, $logoutDate]);

if ($stmt->rowCount() === 1) {
    // Update the existing record with logout time and calculate total hours
    $row = $stmt->fetch();
    $loginDateTime = strtotime("$logoutDate $row[login_time]");
    $logoutDateTime = strtotime("$logoutDate $logoutTime");
    $timeDifference = $logoutDateTime - $loginDateTime;
    $totalHours = number_format(($timeDifference / 3600), 2);

    $sql = "UPDATE user_history SET logout_time = ?, total_hours = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$logoutTime, $totalHours, $row['id']]);
}

session_unset();
session_destroy();

header("Location: index.php?logout=success");
