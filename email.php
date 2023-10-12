<?php
session_start();
require 'connect.php';
date_default_timezone_set('Asia/Dhaka');
use PHPMailer\PHPMailer\PHPMailer;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if (isset($_POST['send'])) {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'finalproject.cse1604@gmail.com';
    $mail->Password = 'diqxtkkdmibzrkde';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true,
        ),
    );

    $mail->setFrom('finalproject.cse1604@gmail.com', 'Attendance Management System');
    $mail->addAddress($_POST['email']);

    $mail->isHTML(true);
    $mail->Subject = $_POST['subject'];
    $mail->Body = $_POST['message'];

    if ($mail->send()) {
        $statusMessage = "Email Sent Successfully";
    } else {
        $statusMessage = "Something went wrong: <br>" . $mail->ErrorInfo;
    }

    $sql = "INSERT INTO sent_email (sender_id, receiver, subject, message, date) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $currentDateTime = date("Y-m-d H:i:s");
    $stmt->execute([$_SESSION['user_id'], $_POST['email'], $_POST['subject'], $_POST['message'], $currentDateTime]);


}

$sql = "SELECT * FROM emp WHERE id=?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$row = $stmt->fetch();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email</title>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap");
        body {
            font-family: Poppins, sans-serif;
            background-color: #f2f2f2;
        }
        .send-mail{
            width: 1200px;
            height: 70vh;
            margin: 60px auto;
            border: 1px solid #cccccc;
            border-radius: 5px;
            display: flex;
            justify-content: first baseline;
            background-color: #f1f1f1;
            box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.2);
        }
        
        .sidebar {
            width: 300px;
            height: 91%;
            background-color: #f1f1f1;
            padding: 30px 0;
            border-right: 1px solid #ccc;
        }
        .img-container {
            width: 100%;
            text-align: center;
        }
        .img-container img {
            background-color: black;
            border-radius: 50%;
            margin: 20px auto;
            display: block;
            padding: 10px;
            opacity: 0;
        }
        .navigation {
            width: 100%;
        }
        .navigation ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .navigation ul li {
            padding: 10px 30px;
            border-bottom: 1px solid #cccccc;
        }
        .navigation ul li a {
            text-decoration: none;
            color: #000;
            font-size: 15px;
            font-weight: 500;
            display: flex;
            align-items: center;
        }
        .navigation ul li a i {
            font-size: 20px;
            margin-right: 10px;
        }
        .navigation ul li:hover {
            background-color: #ccc;
            transition: 0.3s;
        }
        .navigation ul li:hover a {
            color: #000;
        }
        .email {
            width: 100%;
            padding: 30px;
        }
        /* .email {
            max-width: 700px;
            margin: 150px auto;
            background-color: #fff;
            padding: 50px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        } */
        .email h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 20px;
            font-weight: 400;
        }
        .email input[type="text"],
        .email textarea {
            width: 97%;
            padding: 10px;
            margin-bottom: 20px;
            border: none;
            border-radius: 5px;
            font-size: 15px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        .email textarea {
            resize: none;
        }
        .email input[type="submit"] {
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
        }
        .email input[type="submit"]:hover {
            background-color: #3e8e41;
        }
        #edrop {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: none;
            border-radius: 5px;
            font-size: 15px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        #status-msg {
            position: absolute;
            background: #ffc107;
            color: #000;
            padding: 5px;
            width: 200px;
            border-radius: 5px;
            margin: -20px auto;
            text-align: center;
            font-size: 12px;
            left: 45%;
            transition: .3s;
        }
        input:focus,textarea:focus {
            outline: none;
        }
        .sent{
            width: 100%;
            padding: 30px;
            overflow: scroll;
        }
        .sent::-webkit-scrollbar {
            width: 5px;
        }
        .sentBox {
            width: 100%;
            border-collapse: collapse;
            overflow: scroll;
        }
        .sentBox tr:nth-child(odd) {
            background-color: #f2f2f2;
        }
        .sentBox tr:nth-child(even) {
            background-color: #fff;
        }
        .sentBox th {
            padding: 10px;
            text-align: left;
            background-color: #4CAF50;
            color: #fff;
        }
        .sentBox td {
            padding: 10px;
        }
        .sentBox tr:hover {
            background-color: #ccc;
        }
        .sentBox tr:hover td {
            color: #000;
        }
        select{
            outline: none;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
    <script src="https://kit.fontawesome.com/1f9b6a1a6b.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert"></script>
</head>
<body>
    <section class="send-mail">
    <div class="sidebar">
            <div class="img-container">
                <img src="<?php echo $row['img'] ?>" alt="Avatar" style="width:120px">
            </div>
            <div class="navigation">
                <ul>
                    <li><a href="dashboard.php"><i class="fas fa-home"></i>Dashboard</a></li>
                    <li onclick="showCompose()"><a href="#"><i class="fa-regular fa-envelope"></i>New Email</a></li>
                    <li onclick="showSentBox()"><a href="#"><i class="fa-regular fa-paper-plane"></i>Sent Box</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
                </ul>
            </div>
        </div>
        <div class="email">
            <h2>Send Email</h2>
            <form action="" method="post">
                <select name="email" id="edrop">
                    <option value="">Select Name</option>
                    <?php
                    require 'connect.php';
                    $sql = "SELECT * FROM emp";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute();
                    while ($row = $stmt->fetch()) {?>
                        <option value="<?php echo $row['email']; ?>"><?php echo $row['name']; ?></option>
                    <?php }?>
                </select>
                <input type="text" name="subject" placeholder="Enter subject">
                <textarea name="message" id="" cols="30" rows="10" placeholder="Enter message"></textarea>
                <input type="submit" name="send" value="Send">
            </form>
        </div>
        <!-- sent email -->
        <div class="sent" style="display: none;">
            <h2>Sent Email</h2>
            <table class="sentBox">
                <tr>
                    <th>Receiver</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Date</th>
                </tr>
                <?php
                $sql = "SELECT * FROM sent_email WHERE sender_id=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$_SESSION['user_id']]);
                $result = $stmt->fetchAll();
                foreach ($result as $row) {?>
                    <tr>
                        <td><?php echo $row['receiver']; ?></td>
                        <td><?php echo $row['subject']; ?></td>
                        <td><?php echo $row['message']; ?></td>
                        <td><?php echo $row['date']; ?></td>
                    </tr>
                <?php }?>
            </table>
    </section>
    <script>
        function showSentBox() {
            var inbox = document.querySelector('.email');
            var sent = document.querySelector('.sent');
            inbox.style.display = 'none';
            sent.style.display = 'block';
        }

        function showCompose() {
            var inbox = document.querySelector('.email');
            var sent = document.querySelector('.sent');
            inbox.style.display = 'block';
            sent.style.display = 'none';
        }
        <?php if (isset($statusMessage)) {
            echo "swal('Success', '$statusMessage', 'success');";
        }?>
    </script>
    
</body>
</html>
