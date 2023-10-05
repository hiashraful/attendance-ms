<?php
    $db = new PDO('mysql:host=localhost;dbname=attendance_ms;charset=utf8mb4', 'root', '12345');
    
    $username = $_GET['single'];

    $query = "SELECT * FROM `users` where username = '$username'";
    $stmt = $db->query($query);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $username ?></title>
</head>
<body>
    <div class="user">
        <h1>User Name</h1>
        <p>User Details</p>
    </div>
    
</body>
</html>