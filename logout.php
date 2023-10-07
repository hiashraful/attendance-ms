<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

require_once "connect.php";
$userId = $_SESSION['user_id'];
$logoutDate = date("Y-m-d");
$logoutTime = date("H:i:s");
$sql = "SELECT * FROM user_history WHERE user_id = ? AND login_date = ? ORDER BY login_time DESC LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId, $logoutDate]);

if ($stmt->rowCount() === 1) {
    $row = $stmt->fetch();
    $loginDateTime = strtotime("$logoutDate $row[login_time]");
    $logoutDateTime = strtotime("$logoutDate $logoutTime");
    $timeDifference = $logoutDateTime - $loginDateTime;
    $totalHours = number_format(($timeDifference / 3600), 2);

    $sql = "UPDATE user_history SET logout_time = ?, total_hours = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$logoutTime, $totalHours, $row['id']]);
} else {
    $sql = "INSERT INTO user_history (user_id, login_date, login_time, logout_time, total_hours) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, $logoutDate, $logoutTime, $logoutTime, 0]);
}

session_unset();
session_destroy();
header("Location: index.php?logout=success");
exit();
