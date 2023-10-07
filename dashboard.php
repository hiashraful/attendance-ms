<?php
require 'connect.php';
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$sql = "SELECT * FROM emp WHERE email=?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['email']]);
$user = $stmt->fetch();

$sql = "SELECT SUM(total_hours) AS total_hours FROM emp_history WHERE user_id=?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user['id']]);
$totalHours = $stmt->fetch();

$sql = "SELECT SUM(total_hours) AS total_hours FROM emp_history WHERE user_id=? AND MONTH(login_date)=?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user['id'], date('m')]);
$totalHoursMonth = $stmt->fetch();

$sql = "SELECT SUM(total_hours) AS total_hours FROM emp_history WHERE user_id=? AND YEAR(login_date)=?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user['id'], date('Y')]);
$totalHoursYear = $stmt->fetch();

$sql = "SELECT SUM(total_hours) AS total_hours FROM emp_history WHERE user_id=? AND YEARWEEK(login_date)=?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user['id'], date('YW')]);
$totalHoursWeek = $stmt->fetch();

$sql = "SELECT SUM(total_hours) AS total_hours FROM emp_history WHERE user_id=? AND login_date=?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user['id'], date('Y-m-d')]);
$totalHoursDay = $stmt->fetch();

$sql = "SELECT SUM(total_hours) AS total_hours FROM emp_history WHERE user_id=? AND MONTH(login_date)=?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user['id'], date('m', strtotime('-1 month'))]);
$totalHoursPrevMonth = $stmt->fetch();

$dataPoints = array(
    array("x" => 10, "y" => 41),
    array("x" => 20, "y" => 35, "indexLabel" => "Lowest"),
    array("x" => 30, "y" => 50),
    array("x" => 40, "y" => 45),
    array("x" => 50, "y" => 52),
    array("x" => 60, "y" => 68),
    array("x" => 70, "y" => 38),
    array("x" => 80, "y" => 71, "indexLabel" => "Highest"),
    array("x" => 90, "y" => 52),
    array("x" => 100, "y" => 60),
    array("x" => 110, "y" => 36),
    array("x" => 120, "y" => 49),
    array("x" => 130, "y" => 41),
);
for ($i = 13; $i < 31; $i++) {
    $dataPoints[] = array("x" => ($i + 1) * 10, "y" => rand(30, 70));
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
</head>
<body>
    <!-- New Dashboard -->
    <div class="container">
        <!-- ======================= Sidebar ================== -->
    <nav>
        <ul>
            <li>
                <a href="user.php?single=<?php echo $username ?>" class="logo">
                    <img src="<?php echo $user['img'] ?>" alt="">
                    <p class="user-name"><?php echo $username ?></p>
                </a>
            </li>
            <li>
                <a class="nav-list-item" href="#">
                    <i class="fas fa-comment"></i>
                    <span class="nav-item">Notification</span>
                </a>
            </li>
            <li>
                <a class="nav-list-item" href="#">
                <i class="fas fa-users"></i>
                    <span class="nav-item">Users</span>
                </a>
            </li>
            <li>
                <a class="nav-list-item" href="add.php">
                    <i class="fas fa-user-plus"></i>
                    <span class="nav-item">Add User</span>
                </a>
            </li>
            <li>
                <a class="nav-list-item" href="#">
                    <i class="fas fa-database"></i>
                    <span class="nav-item">Report</span>
                </a>
            </li>
            <li>
                <a class="nav-list-item" href="user.php">
                    <i class="fas fa-cog"></i>
                    <span class="nav-item">Setting</span>
                </a>
            </li>

            <li>
                <a  href="logout.php" class="logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="nav-item">Log out</span>
                </a>
            </li>
        </ul>
    </nav>


    <section class="main">
      <div class="main-top">
        <h1>Employee Management System</h1>
        <i class="fas fa-user-cog"></i>
      </div>

      <!-- ======================= Cards ================== -->
      <div class="cardBox">
          <div class="card">
              <div>
                  <div class="numbers">
                    <?php if ($totalHoursDay['total_hours'] == null) {echo 0;} else {echo $totalHoursDay['total_hours'];}?>
                  </div>
                  <div class="cardName">Hours Spent Today</div>
              </div>
              <div class="iconBx">
                  <i class="fas fa-eye"></i>
              </div>
          </div>
          <div class="card">
              <div>
                  <div class="numbers">
                    <?php if ($totalHoursWeek['total_hours'] == null) {echo 0;} else {echo $totalHoursWeek['total_hours'];}?>
                  </div>
                  <div class="cardName">Hours Spent This Week</div>
              </div>

              <div class="iconBx">
                  <ion-icon name="cart-outline"></ion-icon>
                  <i class="fas fa-clock"></i>
              </div>
          </div>

          <div class="card">
              <div>
                  <div class="numbers">
                    <?php if ($totalHoursMonth['total_hours'] == null) {echo 0;} else {echo $totalHoursMonth['total_hours'];}?>
                  </div>
                  <div class="cardName">Hours Spent This Month</div>
              </div>
              <div class="iconBx">
                <i class="fa-solid fa-calendar-check"></i>
              </div>
          </div>
          <div class="card">
              <div>
                <div class="numbers">$
                    <?php if ($totalHoursMonth['total_hours'] == null) {echo 0;} else {echo $totalHoursMonth['total_hours'] * 10;}?>
                </div>
                <div class="cardName">Earnings This Month</div>
              </div>
              <div class="iconBx">
                  <i class="fas fa-money-check-dollar"></i>
              </div>
          </div>
      </div>
      <!-- ======================= Bar Chart ================== -->
      <div id="chartContainer" style="height: 370px; width: 100%;"></div>
      <!-- ===================== Attendance List ================== -->
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
                    $sql = "SELECT * FROM emp_history WHERE logout_time IS NULL";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute();
                    $users = $stmt->fetchAll();

                    foreach ($users as $user) {
                        $sql = "SELECT * FROM emp WHERE id=?";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([$user['user_id']]);
                        $emp = $stmt->fetch();

                        $sql = "SELECT SUM(total_hours) AS total_hours FROM emp_history WHERE user_id=? AND login_date=?";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([$emp['id'], date('Y-m-d')]);
                        $totalHoursDay = $stmt->fetch();
                        echo '<tr>';
                        echo '<td>' . $emp['id'] . '</td>';
                        echo '<td>' . $emp['name'] . '</td>';
                        echo '<td>' . $emp['designation'] . '</td>';
                        echo '<td>' . $user['login_date'] . '</td>';
                        echo '<td>' . $user['login_time'] . '</td>';
                        echo '<td>' . $totalHoursDay['total_hours'] . '</td>';
                        echo '<td><a href="user.php?id=' . $emp['id'] . '">Details</a></td>';
                        echo '</tr>';
                    }
                    ?>
            </tbody>
          </table>
        </div>
      </div>


    </section>
  </div>

  <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
    <script>
        setTimeout(function() {
            var loginMessage = document.getElementById('login-success');
            if (loginMessage) {
                loginMessage.style.display = 'none';
            }
        }, 1500);
        window.onload = function () {

        var chart = new CanvasJS.Chart("chartContainer", {
        animationEnabled: true,
        exportEnabled: true,
        theme: "light1",
        title:{
            text: "Hours Spent This Month"
        },
        axisY:{
            includeZero: true
        },
        data: [{
            type: "column",
            indexLabelFontColor: "#5A5757",
            indexLabelPlacement: "outside",
            dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
        }]
        });
        chart.render();

    }
    </script>
    <script src="https://kit.fontawesome.com/1f9b6a1a6b.js" crossorigin="anonymous"></script>
</body>
</html>