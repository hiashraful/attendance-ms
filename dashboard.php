<?php
require 'connect.php';
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$userId = $_SESSION['user_id'];

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

//we can also make a query to get the location of the user from a specific date

//from latest login
$sql = "SELECT * FROM emp_history WHERE user_id=? AND login_date=? ORDER BY login_time DESC LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user['id'], date('Y-m-d')]);
$location = $stmt->fetch();

//user data for located emp
$sql = "SELECT * FROM emp WHERE id=?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$location['user_id']]);
$userLocation = $stmt->fetch();


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

// Show User Details
$sql = "SELECT * FROM emp WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$row = $stmt->fetch();




?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
    <link
      rel="stylesheet"
      href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
    />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
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
                <a class="nav-list-item" href="email.php">
                    <i class="fas fa-database"></i>
                    <span class="nav-item">Send Email</span>
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
      <!-- ===================== Now Online ================== -->
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
                        echo '<td><button onclick="showDetails()"><i class="fa-solid fa-circle-info"></i></button>
                            <button onclick="showMap()"><i class="fa-solid fa-location-dot"></i></button> </td>';
                        echo '</tr>';
                    }
                    ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
<!-- ======================= Show Location ================== -->
    <section class="location" style="display: none;">
        <h1>Last Location of: <?php echo $_SESSION['username']?> </h1>
        <span class="cross">
            <i class="fa-solid fa-rectangle-xmark" onclick="hideMap()"></i>
        </span>
        <div id="map" style="width: 600px; height: 450px"></div>
    </section>
    <!-- ======================= Show Details ================== -->
    <section class="details" style="display: none;">
        <h1>Details of: <?php echo $_SESSION['username']?> </h1>
        <span class="cross">
            <i class="fa-solid fa-rectangle-xmark" onclick="hideDetails()"></i>
        </span>
        <div class="info">
            <div class="img">
                <img src="<?php echo $row['img'] ?>" alt="Photo of <?php echo $row['name'] ?> " width="150px">
            </div>
            <label for="user-name">ID: </label> 
            <input type="text" name="user-id" value="<?php echo $row['id'] ?>" disabled > <br>
            <label for="user-name">Name: </label> 
            <input type="text" name="user-name" value="<?php echo $row['name'] ?>" disabled > <br>
            <label for="user-designation">Designation: </label> 
            <input type="text" name="user-designation" value="<?php echo $row['designation'] ?>" disabled> <br>
            <label for="user-salary">Salary: </label> 
            <input type="text" name="user-salary" value="<?php echo $row['salary'] ?>" disabled> <br>
            <label for="user-email">Email: </label>
            <input type="email" name="user-email" value="<?php echo $row['email'] ?>" disabled> <br>
        </div>
    </section>
</div>

    <!-- ======================= Show Location ================== -->
    <script>
        var main = document.querySelector(".main");
        function showMap() {
            document.querySelector(".location").style.display = "block";
            main.style.opacity = '0.1';
            var map = L.map("map").setView([<?php if ($location['latitude'] == null) {echo 0;} else {echo $location['latitude'];}?>, <?php if ($location['longitude'] == null) {echo 0;} else {echo $location['longitude'];}?>], 14);
            //lookup for zoom level
            var marker = L.marker([<?php if ($location['latitude'] == null) {echo 0;} else {echo $location['latitude'];}?>, <?php if ($location['longitude'] == null) {echo 0;} else {echo $location['longitude'];}?>]).addTo(map);
            var circle = L.circle([<?php if ($location['latitude'] == null) {echo 0;} else {echo $location['latitude'];}?>, <?php if ($location['longitude'] == null) {echo 0;} else {echo $location['longitude'];}?>], {
                color: "green",
                fillColor: "#cccff",
                fillOpacity: 0.2,
                radius: 500,
            }).addTo(map);

            L.marker([<?php if ($location['latitude'] == null) {echo 0;} else {echo $location['latitude'];}?>, <?php if ($location['longitude'] == null) {echo 0;} else {echo $location['longitude'];}?>], {icon: L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
            iconSize: [25, 41], // Adjust the size as needed
            iconAnchor: [12, 41],
            popupAnchor: [0, -30] // Adjust the anchor point if necessary
        })})
                .addTo(map)
                .bindPopup("<?php if ($userLocation['name'] == null) {echo 0;} else {echo $userLocation['name'];}?> was here<br>logged in at <?php if ($location['login_time'] == null) {echo 0;} else {echo date('h:i A', strtotime($location['login_time']));}?>")
                .openPopup();
            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                attribution:
                '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            }).addTo(map);
            }
            function hideMap() {
                document.querySelector(".location").style.display = "none";
                main.style.opacity = '1';
            }
    </script>
    <!-- ======================= Show Details ================== -->
    <script>
        function showDetails() {
            document.querySelector(".details").style.display = "block";
            main.style.opacity = '0.1';
        }
        function hideDetails() {
            document.querySelector(".details").style.display = "none";
            main.style.opacity = '1';
        }
    </script>
    <!-- ======================= Chart ================== -->
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
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
</body>
</html>