<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// Retrieve the username from the session
$username = $_SESSION['username'];
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
        <!-- Sidebar -->
    <nav>
        <ul>
            <li>
                <a href="user.php?single=<?php echo $username?>" class="logo">
                    <img src="./img/dp.jpg">
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
                <a class="nav-list-item" href="#">
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
                <a class="nav-list-item" href="#">
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
      <div class="users">
        <div class="card">
          <img src="./img/dp.jpg">
          <h4>Sam David</h4>
          <p>Ui designer</p>
          <div class="per">
            <table>
              <tr>
                <td><span>85%</span></td>
                <td><span>87%</span></td>
              </tr>
              <tr>
                <td>Month</td>
                <td>Year</td>
              </tr>
            </table>
          </div>
          <button>Profile</button>
        </div>
        <div class="card">
          <img src="./img/dp.jpg">
          <h4>Balbina kherr</h4>
          <p>Progammer</p>
          <div class="per">
            <table>
              <tr>
                <td><span>82%</span></td>
                <td><span>85%</span></td>
              </tr>
              <tr>
                <td>Month</td>
                <td>Year</td>
              </tr>
            </table>
          </div>
          <button>Profile</button>
        </div>
        <div class="card">
          <img src="./img/dp.jpg">
          <h4>Badan John</h4>
          <p>tester</p>
          <div class="per">
            <table>
              <tr>
                <td><span>94%</span></td>
                <td><span>92%</span></td>
              </tr>
              <tr>
                <td>Month</td>
                <td>Year</td>
              </tr>
            </table>
          </div>
          <button>Profile</button>
        </div>
        <div class="card">
          <img src="./img/dp.jpg">
          <h4>Salina micheal</h4>
          <p>Ui designer</p>
          <div class="per">
            <table>
              <tr>
                <td><span>85%</span></td>
                <td><span>82%</span></td>
              </tr>
              <tr>
                <td>Month</td>
                <td>Year</td>
              </tr>
            </table>
          </div>
          <button>Profile</button>
        </div>
      </div>

      <section class="attendance">
        <div class="attendance-list">
          <h1>Attendance List</h1>
          <table class="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Depart</th>
                <th>Date</th>
                <th>Join Time</th>
                <th>Logout Time</th>
                <th>Details</th>
              </tr>
            </thead>
            <tbody>
              <tr class="active">
                <td>01</td>
                <td>Sam David</td>
                <td>Design</td>
                <td>03-24-22</td>
                <td>8:00AM</td>
                <td>3:00PM</td>
                <td><button>View</button></td>
              </tr>
              <tr>
                <td>02</td>
                <td>Balbina Kherr</td>
                <td>Coding</td>
                <td>03-24-22</td>
                <td>9:00AM</td>
                <td>4:00PM</td>
                <td><button>View</button></td>
              </tr>
              <tr>
                <td>03</td>
                <td>Badan John</td>
                <td>testing</td>
                <td>03-24-22</td>
                <td>8:00AM</td>
                <td>3:00PM</td>
                <td><button>View</button></td>
              </tr>
              <tr>
                <td>04</td>
                <td>Sara David</td>
                <td>Design</td>
                <td>03-24-22</td>
                <td>8:00AM</td>
                <td>3:00PM</td>
                <td><button>View</button></td>
              </tr>
              <!-- <tr >
                <td>05</td>
                <td>Salina</td>
                <td>Coding</td>
                <td>03-24-22</td>
                <td>9:00AM</td>
                <td>4:00PM</td>
                <td><button>View</button></td>
              </tr>
              <tr >
                <td>06</td>
                <td>Tara Smith</td>
                <td>Testing</td>
                <td>03-24-22</td>
                <td>9:00AM</td>
                <td>4:00PM</td>
                <td><button>View</button></td>
              </tr> -->
            </tbody>
          </table>
        </div>
      </section>
    </section>
  </div>


    <!-- Old Code -->
    <!-- <p id="login-success">Logged In Successfully!</p>
    <h1>Test</h1>
    <form action="" method="post">
        <div class="dashboard-container">
            <h1 id="dashboard-heading">Dashboard</h1>
            <p>Welcome, <?php echo $username; ?> </p>
        </div>
    </form>
    <div class="logout">
        <button id="btn-logout"><a href="logout.php">Logout</a></button>
    </div> -->
    <!-- Old Code -->
    <script>
        setTimeout(function() {
            var loginMessage = document.getElementById('login-success');
            if (loginMessage) {
                loginMessage.style.display = 'none';
            }
        }, 1500);
    </script>
</body>
</html>