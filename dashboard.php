<?php
require 'connect.php';
session_start();
date_default_timezone_set('Asia/Dhaka');
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['detail'])) {
    $detail = $_GET['detail'];
    $sql = "SELECT * FROM emp WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$detail]);
    $target = $stmt->fetch();
}
if (isset($_GET['location'])) {
    $locationId = $_GET['location'];
    $sql = "SELECT *
            FROM emp_history
            WHERE user_id = ?
            AND logout_time IS NULL
            ORDER BY login_time DESC
            LIMIT 1;
            ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$locationId]);
    $location = $stmt->fetch();

    $sql = "SELECT * FROM emp WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$location['user_id']]);
    $userLocation = $stmt->fetch();
}
if (isset($_GET['email'])) {
    $email = $_GET['email'];
    $sql = "SELECT * FROM emp WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $emailUser = $stmt->fetch();
}

$username = $_SESSION['username'];
$userId = $_SESSION['user_id'];
$userEmail = $_SESSION['email'];


$sql = "SELECT * FROM emp WHERE email=?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['email']]);
$user = $stmt->fetch();

$sql = "SELECT SUM(total_hours) AS total_hours FROM emp_history WHERE user_id=? AND MONTH(login_date)=?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId, date('m')]);
$totalHoursMonth = $stmt->fetch();

$sql = "SELECT SUM(total_hours) AS total_hours FROM emp_history WHERE user_id = ? AND login_date = ?;";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId, date('Y-m-d')]);
$totalHoursDay = $stmt->fetch();

//we can also make a query to get the location of the user from a specific date

//from latest login

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
    <script src="https://kit.fontawesome.com/1f9b6a1a6b.js" crossorigin="anonymous"></script>
</head>
<body>
    <!-- New Dashboard -->
    <div class="container">
            <!-- ======================= Sidebar ================== -->
        <nav id="menu">
            <ul>
                <li>
                    <a href="user.php?single=<?php echo $username ?>" class="logo">
                        <img src="<?php echo $user['img'] ?>" alt="">
                        <p class="user-name"><?php echo $username ?></p>
                    </a>
                    <div class="menu-toggle" id="menuToggle">
                        <div class="bar"></div>
                        <div class="bar"></div>
                        <div class="bar"></div>
                    </div>
                </li>
                <li>
                <a class="nav-list-item" href="dashboard.php">
                        <i class="fas fa-home"></i>
                        <span class="nav-item">Home</span>
                    </a>
                </li>
                <li>
                    <a class="nav-list-item" href="#">
                        <i class="fas fa-comment"></i>
                        <span class="nav-item">Notification</span>
                    </a>
                </li>
                <li>
                    <a class="nav-list-item" href="showUser.php">
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
                        <i class="fas fa-envelope"></i>
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
            </div>
            <!-- ======================= Cards for user ================== -->
            <div class="cardBox user" style="display: none;">
                <div class="card">
                    <div>
                        <div class="numbers">
                            <!-- first login time today -->
                            <?php
                                $sql = "SELECT * FROM emp_history WHERE user_id=? AND login_date=? ORDER BY login_time ASC LIMIT 1";
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute([$user['id'], date('Y-m-d')]);
                                $today = $stmt->fetch();
                                if (isset($today['login_time']) == null) {
                                    echo '00:00:00';
                                } else {
                                    echo date('h:i A', strtotime($today['login_time']));
                                }
                                ?>
                        </div>
                        <div class="cardName">Logged In Today</div>
                    </div>

                    <div class="iconBx">
                        <ion-icon name="cart-outline"></ion-icon>
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="card">
                    <div>
                        <div class="numbers">
                            <?php if ($totalHoursDay['total_hours'] == null) {echo 0;} else {echo $totalHoursDay['total_hours'];}?>
                        </div>
                        <div class="cardName">Time Registered Today</div>
                    </div>
                    <div class="iconBx">
                        <i class="fas fa-eye"></i>
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
            <!-- ======================= Cards for admin ================== -->
            <div class="cardBox admin" style="display: none;">
                <div class="card">
                    <div>
                        <div class="numbers">
                            <?php
                                $sql = "SELECT * FROM emp";
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute();
                                $totalEmp = $stmt->rowCount();
                                echo $totalEmp;
                                ?>
                        </div>
                        <div class="cardName">Total Employee</div>
                    </div>
                    <div class="iconBx">
                        <i class="fas fa-eye"></i>
                    </div>
                </div>

                <div class="card">
                    <div>
                        <div class="numbers">
                        <?php
                            $sql = "SELECT COUNT(DISTINCT user_id) 
                            FROM emp_history 
                            WHERE login_time > '17:00:00' 
                            AND login_date = CURDATE()";
                    
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute();
                            $count = $stmt->fetchColumn();
                            echo $count;
                        ?>
                        </div>
                        <div class="cardName">Late Today</div>
                    </div>

                    <div class="iconBx">
                        <ion-icon name="cart-outline"></ion-icon>
                        <i class="fas fa-clock"></i>
                    </div>
                </div>

                <div class="card">
                    <div>
                        <div class="numbers">
                        <!-- employees who are online -->
                        <?php
                                $sql = "SELECT eh.*
                                FROM emp_history eh
                                JOIN (
                                    SELECT user_id, MAX(login_time) AS latest_login_time
                                    FROM emp_history
                                    WHERE logout_time IS NULL
                                    GROUP BY user_id
                                ) latest_login
                                ON eh.user_id = latest_login.user_id AND eh.login_time = latest_login.latest_login_time;";

                                $stmt = $pdo->prepare($sql);
                                $stmt->execute();
                                $online = $stmt->rowCount();
                                echo $online;
                                ?>
                        </div>
                        <div class="cardName">Now Online</div>
                    </div>
                    <div class="iconBx">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                </div>
                <div class="card">
                    <div>
                        <div class="numbers">
                        <!-- Total Work Hours This Month -->
                        <?php
                            $sql = "SELECT SUM(total_hours) AS total_hours FROM emp_history WHERE MONTH(login_date)=?";
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute([date('m')]);
                            $totalHoursMonth = $stmt->fetch();
                            echo $totalHoursMonth['total_hours'] * 10;
                            ?>
                        hrs</div>
                        <div class="cardName">Work Hours This Month</div>
                    </div>
                    <div class="iconBx">
                        <i class="fas fa-money-check-dollar"></i>
                    </div>
                </div>
            </div>
            <!-- ======================= Bar Chart ================== -->
            <div class="select-days">
                <form action="" method="post">
                    <select name="selectedDay" id="days" onchange="this.form.submit()">Select Days
                        <option >Select Days</option>
                        <option value="7">7</option>
                        <option value="15">15</option>
                        <option value="30">30</option>
                    </select>
                </form>
                <?php if(isset($_POST['selectedDay'])){
                    $noOfDay = intval($_POST['selectedDay']);
                }?>
            </div>
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
                            $sql = "SELECT eh.*
                            FROM emp_history eh
                            JOIN (
                                SELECT user_id, MAX(login_time) AS latest_login_time
                                FROM emp_history
                                WHERE logout_time IS NULL
                                GROUP BY user_id
                            ) latest_login
                            ON eh.user_id = latest_login.user_id AND eh.login_time = latest_login.latest_login_time;";

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
                                echo '<td id="buttons">
                                    <a href="showUser.php?detail=' . $emp['id'] . ' "><i class="fa-solid fa-circle-info"></i></a>
                                    <a href="showUser.php?location=' . $emp['id'] . ' "><i class="fa-solid fa-location-dot"></i></a>
                                    <a href="showUser.php?email=' . $emp['id'] . ' "><i class="fa-solid fa-envelope"></i></a>
                                    </td>';
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
            <h1>Last Location of: <?php echo $userLocation['name'] ?> </h1>
            <span class="cross">
                <i class="fa-solid fa-rectangle-xmark" onclick="hideMap()"></i>
            </span>
            <div id="map" style="width: 600px; height: 450px"></div>
        </section>
        <!-- ======================= Send Email ================== -->
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

        <!-- ======================= Show Details ================== -->
        <section class="details" style="display: none;">
            <h1>Details of: <?php echo $target['name'] ?> </h1>
            <span class="cross">
                <i class="fa-solid fa-rectangle-xmark" onclick="hideDetails()"></i>
            </span>
            <div class="info">
                <div class="img">
                    <img src="<?php echo $target['img'] ?>" alt="Photo of <?php echo $target['name'] ?> " width="150px">
                </div>
                <label for="user-name">ID: </label>
                <input type="text" name="user-id" value="<?php echo $target['id'] ?>" disabled > <br>
                <label for="user-name">Name: </label>
                <input type="text" name="user-name" value="<?php echo $target['name'] ?>" disabled > <br>
                <label for="user-designation">Designation: </label>
                <input type="text" name="user-designation" value="<?php echo $target['designation'] ?>" disabled> <br>
                <label for="user-salary">Salary: </label>
                <input type="text" name="user-salary" value="<?php echo $target['salary'] ?>" disabled> <br>
                <label for="user-email">Email: </label>
                <input type="text" name="user-email" value="<?php echo $target['email'] ?>" disabled> <br>
            </div>
        </section>
    </div>
    <!-- ======================= Send Email ================== -->
    <script>
        var main = document.querySelector(".main");
        function sendEmail() {
            document.querySelector(".email").style.display = "block";
            main.style.opacity = '0.1';
        }
        function hideEmail() {
            document.querySelector(".email").style.display = "none";
            main.style.opacity = '1';
        }
        <?php if (isset($_GET['email'])) {?>
            sendEmail();
        <?php }?>
    </script>
    <!-- ======================= Show Location ================== -->
    <script>
        var main = document.querySelector(".main");
        function showMap() {
            document.querySelector(".location").style.display = "block";
            main.style.opacity = '0.1';
            var map = L.map("map").setView([<?php if (isset($location['latitude']) == null) {echo 0;} else {echo $location['latitude'];}?>, <?php if (isset($location['longitude']) == null) {echo 0;} else {echo $location['longitude'];}?>], 14);
            //lookup for zoom level
            var marker = L.marker([<?php if (isset($location['latitude']) == null) {echo 0;} else {echo $location['latitude'];}?>, <?php if (isset($location['longitude']) == null) {echo 0;} else {echo $location['longitude'];}?>]).addTo(map);
            var circle = L.circle([<?php if (isset($location['latitude']) == null) {echo 0;} else {echo $location['latitude'];}?>, <?php if (isset($location['longitude']) == null) {echo 0;} else {echo $location['longitude'];}?>], {
                color: "green",
                fillColor: "#cccff",
                fillOpacity: 0.2,
                radius: 500,
            }).addTo(map);

            L.marker([<?php if (isset($location['latitude']) == null) {echo 0;} else {echo $location['latitude'];}?>, <?php if (isset($location['longitude']) == null) {echo 0;} else {echo $location['longitude'];}?>], {icon: L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
            iconSize: [25, 41], // Adjust the size as needed
            iconAnchor: [12, 41],
            popupAnchor: [0, -30] // Adjust the anchor point if necessary
        })})
                .addTo(map)
                .bindPopup("<?php if (isset($userLocation['name']) == null) {echo 0;} else {echo $userLocation['name'];}?> was here<br>logged in at <?php if (isset($location['login_time']) == null) {echo 0;} else {echo date('h:i A', strtotime($location['login_time']));}?>")
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
            <?php if (isset($_GET['location'])) {?>
                showMap();
            <?php }?>
    </script>
    <!-- ======================= Show Details ================== -->
    <script>
        var main = document.querySelector(".main");
        function showDetails() {
            document.querySelector(".details").style.display = "block";
            main.style.opacity = '0.1';
        }
        function hideDetails() {
            document.querySelector(".details").style.display = "none";
            main.style.opacity = '1';
        }
        <?php if (isset($_GET['detail'])) {?>
            showDetails();
        <?php }?>
    </script>
    <!-- ======================= Chart ================== -->
    <script>
        window.onload = function () {

        var chart = new CanvasJS.Chart("chartContainer", {
            animationEnabled: true,
            theme: "light2", // "light1", "light2", "dark1", "dark2"
            title:{
                text: <?= ($_SESSION['role'] == 'admin') ? '"Hours Worked By Employees"' : '"Hours Worked By You"'?>
            },
            axisY: {
                title: "Hours Worked"
            },
            data: [{        
                type: "column",  
                showInLegend: true, 
                legendMarkerColor: "grey",
                legendText: <?= ($_SESSION['role'] == 'admin') ? '"Employees"' : '"Dates"'; ?>,
                dataPoints: [      
                    <?php
                        if($_SESSION['role'] == 'admin'){
                            if (isset($noOfDay)) {
                                $sql = "SELECT emp.name AS employee_name, IFNULL(SUM(eh.total_hours), 0) AS total_hours
                                        FROM emp
                                        LEFT JOIN emp_history eh ON emp.id = eh.user_id
                                        WHERE eh.login_date >= DATE(NOW() - INTERVAL " . $noOfDay . " DAY)
                                        GROUP BY emp.id";
                        
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute();
                        
                                while ($row = $stmt->fetch()) {
                                    echo '{ y:' . $row['total_hours'] . ', label: "'. $row['employee_name'] .'" },';
                                }
                            }else{
                                $sql = "SELECT emp.name AS employee_name, SUM(eh.total_hours) AS total_hours
                                FROM emp
                                LEFT JOIN emp_history eh ON emp.id = eh.user_id
                                WHERE MONTH(eh.login_date) = ? 
                                GROUP BY emp.id";
                        
                                $stmt = $pdo->prepare($sql);
                                $currentMonth = date('m'); // Get the current month
                                $stmt->execute([$currentMonth]);
                                
                                while ($row = $stmt->fetch()) {
                                    echo '{ y:' . $row['total_hours'] . ', label: "'. $row['employee_name'] .'" },';
                                } 
                            }
                        } else{                            
                            if(isset($noOfDay)){
                                $sql = "SELECT d.d AS emp_date, IFNULL(SUM(eh.total_hours), 0) AS total_hours
                                FROM (
                                    SELECT DATE(NOW() - INTERVAL (n - 1) DAY) AS d
                                    FROM (
                                        SELECT 1 AS n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5
                                        UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10
                                        UNION SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION SELECT 15
                                        UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION SELECT 20
                                        UNION SELECT 21 UNION SELECT 22 UNION SELECT 23 UNION SELECT 24 UNION SELECT 25
                                        UNION SELECT 26 UNION SELECT 27 UNION SELECT 28 UNION SELECT 29 UNION SELECT 30
                                    ) AS numbers
                                ) AS d
                                LEFT JOIN (
                                    SELECT login_date, total_hours
                                    FROM emp_history
                                    WHERE user_id =".$_SESSION['user_id']." AND login_date >= DATE(NOW() - INTERVAL ".$noOfDay." DAY)
                                ) eh ON d.d = eh.login_date
                                GROUP BY d.d
                                ORDER BY d.d DESC
                                LIMIT ".$noOfDay.";
                                ";
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute();
                                while ($row = $stmt->fetch()) {
                                    echo '{ y:' . $row['total_hours'] . ', label: "'. $row['emp_date'] .'" },';
                                }
                            } else{
                                $sql = "SELECT eh.login_date AS emp_date, SUM(eh.total_hours) AS total_hours
                                FROM emp_history eh
                                WHERE MONTH(eh.login_date) = ?
                                GROUP BY eh.login_date
                                ORDER BY eh.login_date DESC
                                ";
                        
                                $stmt = $pdo->prepare($sql);
                                $currentMonth = date('m'); // Get the current month
                                $stmt->execute([$currentMonth]);
                                
                                while ($row = $stmt->fetch()) {
                                    echo '{ y:' . $row['total_hours'] . ', label: "'. $row['emp_date'] .'" },';
                                }
                            }

                        }
                        
                    ?>
                ]
            }]
        });
        
        chart.render();

        }
    </script>

    <!-- ====================== Admin User Script ====================== -->
    <script>
        function showAdmin() {
            document.querySelector(".admin").style.display = "grid";
            document.querySelector(".user").style.display = "none";
        }
        function showUser() {
            document.querySelector(".admin").style.display = "none";
            document.querySelector(".user").style.display = "grid";
        }
        <?php
            if ($_SESSION['role'] == 'admin'){
                echo 'showAdmin();';
            } else {
                echo 'showUser();';
           }
        ?>

    </script>
    <!-- ====================== Menu for mobile====================== -->
    <script>
        const menuToggle = document.getElementById("menuToggle");
        const menu = document.querySelector("nav");

        menuToggle.addEventListener("click", () => {
            menu.classList.toggle("show");
            main.classList.toggle("opacityBack");
            
        });
    </script>
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
</body>
</html>