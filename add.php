<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

require 'connect.php';

if (isset($_POST['user-name'])) {
    $name = $_POST['user-name'];
    $designation = $_POST['user-designation'];
    $salary = $_POST['user-salary'];
    $email = $_POST['user-email'];
    $password = $_POST['user-password'];
    $uploadDir = 'img/';
    $uploadFile = $uploadDir . basename($_FILES['image']['name']);
    $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

    if (in_array($imageFileType, array('jpg', 'jpeg', 'png', 'gif'))) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $imageUrl = $uploadFile;
            $sql = "INSERT INTO emp (name, designation, salary, email, password, img) VALUES(?, ?, ?, ?, ?, ?);";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute([$name, $designation, $salary, $email, $password, $imageUrl])) {
                echo 'User added successfully.';
            } else {
                echo 'Error creating user.';
            }
        } else {
            echo 'Error uploading the image';
        }
    } else {
        echo 'Invalid file format. Only JPG, JPEG, PNG, and GIF files are allowed.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
    <section class="add-user">
        <form method="POST" enctype="multipart/form-data">
            <h1>Add New User</h1>
            <label for="user-name">Name: </label>
            <input type="text" name="user-name"> <br>
            <label for="user-designation">Designation: </label>
            <input type="text" name="user-designation"> <br>
            <label for="user-salary">Salary: </label>
            <input type="text" name="user-salary"> <br>
            <label for="user-email">Email: </label>
            <input type="email" name="user-email"> <br>
            <label for="user-password">Password: </label>
            <input type="password" name="user-password"> <br>
            <label for="user-image">Image: </label>
            <input type="file" name="image" accept="image/*" required> <br>
            <input type="submit" value="Upload">
        </form>
    </section>
    </div>
</body>
</html>
