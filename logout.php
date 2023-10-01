<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}
session_unset();
session_destroy();

header("Location: index.php?logout=success");
