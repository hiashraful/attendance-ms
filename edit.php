<?php
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
require_once "connect.php";
$sql = "SELECT * FROM emp WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$row = $stmt->fetch();

//update user information
if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $designation = $_POST['designation'];
    $salary = $_POST['salary'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    //update user information in database
    $sql = "UPDATE emp SET name = ?, designation = ?, salary = ?, email = ?, password = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $designation, $salary, $email, $password, $userId]);

    //redirect to user.php
    header("Location: user.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $username ?>
    </title>
    <style>
        
    </style>
</head>
<body>
    <form method="post">
        <label for="name">Name: </label>
        <input type="text" name="name" value="<?php echo $row['name'] ?>"> <br>
        <label for="designation">Designation: </label>
        <input type="text" name="designation" value="<?php echo $row['designation'] ?>"> <br>
        <label for="salary">Salary: </label>
        <input type="text" name="salary" value="<?php echo $row['salary'] ?>"> <br>
        <label for="email">Email: </label>
        <input type="email" name="email" value="<?php echo $row['email'] ?>"> <br>
        <label for="password">Password: </label>
        <input type="password" name="password" value="<?php echo $row['password'] ?>"> <br>
        <input type="submit" name="update" value="Update">
    </form>
</body>
</html>