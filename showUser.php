<?php
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
//these are from dashboard.php



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Employees</title>
</head>
<body>
    <div class="attendance">
        <div class="attendance-list">
          <h1>Now Online</h1>
          <table class="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Designation</th>
                <th>Date</th>
                <th>Join Time</th>
                <th>Hours Today</th>
                <th>Details</th>
              </tr>
            </thead>
            <tbody>
              <?php
                    //select all employees from 
                    require_once "connect.php";
                    $sql = "SELECT * FROM emp";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute();
                    $users = $stmt->fetchAll();

                    foreach ($users as $user) {
                        $sql = "SELECT * FROM emp_history WHERE user_id = ? ORDER BY login_time DESC LIMIT 1";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([$user['id']]);
                        $emp = $stmt->fetch();
                        echo '<tr>';
                        echo '<td>' . $user['id'] . '</td>';
                        echo '<td>' . $user['name'] . '</td>';
                        echo '<td>' . $user['designation'] . '</td>';
                        echo '<td>' . $emp['login_date'] . '</td>';
                        echo '<td>' . $emp['login_time'] . '</td>';
                        echo '<td>' . $emp['total_hours'] . '</td>';
                        echo '<td>
                            <a href="showUser.php?detail=' . $emp['user_id'] . ' "><i class="fa-solid fa-circle-info"></i></a>
                            <a href="showUser.php?location=' . $emp['user_id'] . ' "><i class="fa-solid fa-location-dot"></i></a>
                            <a href="showUser.php?email=' . $emp['user_id'] . ' "><i class="fa-solid fa-envelope"></i></a>
                            </td>';
                        echo '</tr>';
                    }
                    ?>
            </tbody>
          </table>
        </div>
      </div>
</body>
</html>