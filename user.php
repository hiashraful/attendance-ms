<?php
require 'connect.php';
session_start();
$username = $_SESSION['username'];
$userId = $_SESSION['user_id'];
if ($username == null) {
    header("Location: index.php");
    exit;
}

$sql = "SELECT * FROM emp WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$row = $stmt->fetch();

if (isset($_POST['update'])) {
    $name = $_POST['user-name'];
    $designation = $_POST['user-designation'];
    $salary = $_POST['user-salary'];
    $email = $_POST['user-email'];
    $uploadDir = 'img/';
    $uploadFile = $uploadDir . basename($_FILES['image']['name']);
    $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

    if (in_array($imageFileType, array('jpg', 'jpeg', 'png', 'gif'))) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $imageUrl = $uploadFile;
            $sql = "UPDATE emp SET name = ?, designation = ?, salary = ?, email = ?, img = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$name, $designation, $salary, $email, $imageUrl, $userId])) {
                echo 'User updated successfully.';
            } else {
                echo 'Error updating user.';
            }
        } else {
            echo 'Error uploading the image';
        }
    } else {
        echo 'Invalid file format. Only JPG, JPEG, PNG, and GIF files are allowed.';
    }
}

//change password
if (isset($_POST['change-password'])) {
    $password = $_POST['user-password'];
    $newPassword = $_POST['user-new-password'];
    $confirmPassword = $_POST['user-confirm-password'];

    if ($newPassword == $confirmPassword) {
        $sql = "SELECT * FROM emp WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        $row = $stmt->fetch();

        if ($password == $row['password']) {
            // $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE emp SET password = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$newPassword, $userId])) {
                $message = 'Password changed successfully.';
            } else {
                $message = 'Error changing password.';
            }
        } else {
            $message = 'Incorrect password.';
        }
    } else {
        $message = 'New password and confirm password do not match.';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $username ?></title>
    <link rel="stylesheet" href="user.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
</head>
<body>
    <div class="container">
        <h3>Account Settings</h3>
        <section class="profile">
            <div class="sidebar">
                <div class="img-container">
                    <img src="<?php echo $row['img'] ?>" alt="Avatar" style="width:180px">
                </div>
                <div class="navigation">
                    <ul>
                        <li><a href="dashboard.php"><i class="fas fa-home"></i>Dashboard</a></li>
                        <li onclick="showBlock('info')"><a href="#"><i class="fas fa-user"></i>General</a></li>
                        <li onclick="showBlock('password')"><a href="#"><i class="fas fa-cog"></i>Password Change</a></li>
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
            <div class="details" >
                <?php if (isset($message)) {?>
                    <p id="error"><?php echo $message; ?></p>
                <?php }?>
                <div class="info">
                    <label for="user-name">Name: </label> <br>
                    <input type="text" name="user-name" value="<?php echo $row['name'] ?>" disabled > <br>
                    <label for="user-designation">Designation: </label> <br>
                    <input type="text" name="user-designation" value="<?php echo $row['designation'] ?>" disabled> <br>
                    <label for="user-salary">Salary: </label> <br>
                    <input type="text" name="user-salary" value="<?php echo $row['salary'] ?>" disabled> <br>
                    <label for="user-email">Email: </label> <br>
                    <input type="email" name="user-email" value="<?php echo $row['email'] ?>" disabled> <br>
                    <input type="submit" value="Edit Details" onclick="showEditor()">
                </div>
                <div class="password" style="display: none;">
                    <form method="POST">
                        <label for="user-password">Current Password: </label> <br>
                        <input type="password" name="user-password" required> <br>
                        <label for="user-new-password">New Password: </label> <br>
                        <input type="password" name="user-new-password" required> <br>
                        <label for="user-confirm-password">Confirm Password: </label> <br>
                        <input type="password" name="user-confirm-password" required> <br>
                        <input type="submit" value="Change Password" name="change-password">
                    </form>
                </div>
            </div>
        </section>
    <section class="update" style="display: none;">
        <form method="POST" enctype="multipart/form-data">
            <h1>Update Information</h1>
            <span id="cross">
                <i class="fa-solid fa-rectangle-xmark" onclick="hideEditor()"></i>
            </span>
            <label for="user-name">Name: </label>
            <input type="text" name="user-name" value="<?php if (isset($row['name'])) {echo $row['name'];}?>"> <br>
            <label for="user-designation">Designation: </label>
            <input type="text" name="user-designation" value="<?php if (isset($row['designation'])) {echo $row['designation'];}?>"> <br>
            <label for="user-salary">Salary: </label>
            <input type="text" name="user-salary" value="<?php if (isset($row['salary'])) {echo $row['salary'];}?>"> <br>
            <label for="user-email">Email: </label>
            <input type="email" name="user-email" value="<?php if (isset($row['email'])) {echo $row['email'];}?>"> <br>
            <label for="user-image">Image: </label>
            <input type="file" name="image" accept="image/*"> <br>
            <input type="submit" value="Submit" name="update">
        </form>
    </section>
    </div>
    <script src="https://kit.fontawesome.com/1f9b6a1a6b.js" crossorigin="anonymous"></script>
    <script>
        var editor = document.querySelector('.update');
        var profile = document.querySelector('.profile');
        var info = document.querySelector('.info');
        var password = document.querySelector('.password');
        function showEditor(){
            editor.style.display = 'block';
            profile.style.opacity = '0.3';
        }
        function hideEditor(){
            editor.style.display = 'none';
            profile.style.opacity = '1';
        }
        function showBlock(blockName) {
            if (blockName === 'info') {
                document.querySelector('.info').style.display = 'block';
                document.querySelector('.password').style.display = 'none';
            } else if (blockName === 'password') {
                document.querySelector('.info').style.display = 'none';
                document.querySelector('.password').style.display = 'block';
            }
        }
    </script>
</body>
</html>