<?php
require 'connect.php';
session_start();
//retrieve session data
$username = $_SESSION['username'];
$userId = $_SESSION['user_id'];

//make sure user is logged in, otherwise redirect to login page
if ($username == null) {
    header("Location: index.php");
    exit;
}

//retrieve emp information from database
$sql = "SELECT * FROM emp WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$row = $stmt->fetch();

//update user information
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
            
            // Modify the SQL query to exclude the password field
            $sql = "UPDATE emp SET name = ?, designation = ?, salary = ?, email = ?, img = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            
            // Execute the query without including the password
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
    <h1>Welcome <?php echo $username ?></h1>
    <section class="profile">
        <div class="card">
            <img src="<?php echo $row['img'] ?>" alt="Avatar" style="width:150px">
            <input type="button" value="Edit Profile" onclick="showEditor()">
            <div class="container">
                <h4><b><?php echo $row['name'] ?></b></h4>
                <p><?php echo $row['designation'] ?></p>
                <p><?php echo $row['salary'] ?></p>
                <p><?php echo $row['email'] ?></p>
                <p><?php echo $row['password'] ?></p>
            </div>
        </div>
    </section>
    <section class="update" style="display: none;">
        <form method="POST" enctype="multipart/form-data">
            <h1>Add New User</h1>
            <label for="user-name">Name: </label>
            <input type="text" name="user-name" value="<?php 
                if(isset($row['name'])){
                    echo $row['name'];
                }
             ?>"> <br>
            <label for="user-designation">Designation: </label>
            <input type="text" name="user-designation" value="
                <?php 
                    if(isset($row['designation'])){
                        echo $row['designation'];
                    }
                ?>
            "> <br>
            <label for="user-salary">Salary: </label>
            <input type="text" name="user-salary" value="
                <?php 
                    if(isset($row['salary'])){
                        echo $row['salary'];
                    }
                ?>
            "> <br>
            <label for="user-email">Email: </label>
            <input type="email" name="user-email" value="
                <?php 
                    if(isset($row['email'])){
                        echo $row['email'];
                    }
                ?>
            "> <br>
            <label for="user-image">Image: </label>
            <input type="file" name="image" accept="image/*"> <br>
            <input type="submit" value="Upload" name="update">
        </form>
    </section>
    <button> <a href="logout.php">Logout</a> </button>
    </div>
    <script src="https://kit.fontawesome.com/1f9b6a1a6b.js" crossorigin="anonymous"></script>
    <script>
        var editor = document.querySelector('.update');
        function showEditor(){
            editor.style.display = 'block';
        }
        function hideEditor(){
            editor.style.display = 'none';
        }
    </script>
</body>
</html>