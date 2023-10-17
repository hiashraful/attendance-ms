<?php
session_start();
include "connect.php";
date_default_timezone_set('Asia/Dhaka');
if (isset($_POST['u_email']) && (isset($_POST['u_password']) || isset($_POST['u_fingerprint_id']))) {
    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $email = validate($_POST['u_email']);

    if (isset($_POST['u_password'])) {
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
                header("Location: dashboard.php");
                exit();
            } else {
                header("Location: index.php?error=Incorrect User name or password");
                exit();
            }
        }
    } elseif (isset($_POST['id'])) {
        $fingerprint_id = intval($_POST['id']); 

        $sql = "SELECT * FROM emp WHERE fingerprint_id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$fingerprint_id]);

        if ($stmt->rowCount() === 1) {
            $row = $stmt->fetch();

            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['username'] = $row['name'];
            $_SESSION['role'] = $row['role'];

            $loginDate = date("Y-m-d");
            $loginTime = date("H:i:s");
            $sql = "INSERT INTO emp_history (user_id, login_date, login_time) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$_SESSION['user_id'], $loginDate, $loginTime]);
            header("Location: dashboard.php");
            exit();
        }
} else {
    header("Location: index.php");
    exit();
}
}