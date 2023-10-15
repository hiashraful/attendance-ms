<?php
require 'connect.php';
//these are from dashboard.php
if (isset($_GET['detail'])) {
    $detail = $_GET['detail'];
    header("Location: dashboard.php?detail=$detail");
    exit;
}
if (isset($_GET['location'])) {
    $location = $_GET['location'];
    header("Location: dashboard.php?location=$location");
    exit;
}
if (isset($_GET['email'])) {
    $email = $_GET['email'];
    header("Location: dashboard.php?email=$email");
    exit;
}

if (isset($_GET['notification'])) {
  $notificationId = $_GET['notification'];
  $sql = "SELECT * FROM emp WHERE id = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$notificationId]);
  $emailUser = $stmt->fetch();
}
//these are from to dashboard.php

$sql = "SELECT id AS user_id FROM emp";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$cardData = $stmt->fetchAll();
$heading = "All Employees";

//from admin card
//now online
if (isset($_GET['onlineEmp'])) {
  $onlineUserData = urldecode($_GET['onlineEmp']);
  $cardData = json_decode($onlineUserData, true);
  $heading = "Online Employees";
}

//less than 35 hours
if(isset($_GET['lessEmp'])){
  $lessEmpData = urldecode($_GET['lessEmp']);
  $cardData = json_decode($lessEmpData, true);
  $heading = "Employees with less than 35 hours";
}

//late today
if(isset($_GET['lateEmp'])){
  $lateEmpData = urldecode($_GET['lateEmp']);
  $cardData = json_decode($lateEmpData, true);
  $heading = "Employees who are late today";
}

//get details from cardData
$currentMonth = date("m");
$userData = [];

foreach ($cardData as $user) {
    $userId = $user['user_id'];
    $sql1 = "SELECT * FROM emp WHERE id = ?";
    $sql2 = "SELECT * FROM emp_history WHERE user_id = ? AND MONTH(login_date) = ?";
    
    $empStmt = $pdo->prepare($sql1);
    $empStmt->execute([$userId]);
    $ehStmt = $pdo->prepare($sql2);
    $ehStmt->execute([$userId, $currentMonth]);

    $empData = $empStmt->fetch();
    $empHistoryData = $ehStmt->fetchAll();
    
    $totalHours = 0;
    foreach ($empHistoryData as $historyEntry) {
        if (!empty($historyEntry['total_hours'])) {
            $totalHours += floatval($historyEntry['total_hours']);
        }
    }

    $userData[] = [
        'emp_data' => $empData,
        'emp_history_data' => $empHistoryData,
        'total_hours' => $totalHours,
    ];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Employees</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
    <link rel="stylesheet" href="showUser.css">
    <script src="https://kit.fontawesome.com/1f9b6a1a6b.js" crossorigin="anonymous"></script>
</head>
<body>
  <div class="email" style="display: none;">
      <h1>Send an email to: <?php echo $emailUser['name'] ?> </h1>
      <span class="cross">
          <i class="fa-solid fa-rectangle-xmark" onclick="hideEmail()"></i>
      </span>
      <div class="send">
          <form action="email.php" method="post">
              <input type="email" name="email" value="<?php echo $emailUser['email'] ?>">
              <input type="text" name="subject" placeholder="Enter subject">
              <textarea name="message" id="" cols="30" rows="10" placeholder="Enter message"></textarea>
              <input type="submit" name="send" value="Send">
          </form>
      </div>
  </div>
  <div class="employee">
    <a href="dashboard.php" id="home"><i class="fas fa-home"></i>Dashboard</a>
    <div class="heading">
      <h1><?= $heading; ?></h1>
    </div>
    <div class="show">
      <table border="1">
        <thead>
            <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>Designation</th>
                <th>Role</th>
                <th>Salary</th>
                <th>Email</th>
                <th>Total Hours (This Month)</th>
                <th>Send Email</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($userData as $user) : ?>
                <tr>
                    <td><?= $user['emp_data']['id']; ?></td>
                    <td><?= $user['emp_data']['name']; ?></td>
                    <td><?= $user['emp_data']['designation']; ?></td>
                    <td><?= $user['emp_data']['role']; ?></td>
                    <td><?= $user['emp_data']['salary']; ?></td>
                    <td><?= $user['emp_data']['email']; ?></td>
                    <td><?= $user['total_hours']; ?></td>
                    <?php
                      echo '<td id="buttons">
                      <a href="showUser.php?notification=' . $user['emp_data']['id'] . ' "><i class="fa-solid fa-envelope"></i></a>
                      </td>';
                    ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
        var emp = document.querySelector(".employee");
        function sendEmail() {
            document.querySelector(".email").style.display = "block";
            emp.style.opacity = '0.05';
        }
        function hideEmail() {
            document.querySelector(".email").style.display = "none";
            emp.style.opacity = '1';
        }
        <?php if (isset($_GET['notification'])) {?>
            sendEmail();
        <?php }?>
    </script>
</body>
</html>