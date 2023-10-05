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
        $sql = "SELECT * FROM users WHERE email=? AND password=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email, $password]);

		if ($stmt->rowCount() === 1) {
			$row = $stmt->fetch();
			if ($row['email'] === $email && $row['password'] === $password) {
				$_SESSION['email'] = $row['email'];
				$_SESSION['username'] = $row['name'];
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
