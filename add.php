<?php
require 'connect.php';
if(isset($_POST['user-name'])){
    $name = $_POST['user-name'];
    $designation = $_POST['user-designation'];
    $salary = $_POST['user-salary'];

    $sql = "INSERT INTO user (name, designation, salary) VALUES(?, ?, ?);";
    $stmt = $pdo->prepare($sql);
    if($stmt->execute([$name, $designation, $salary])){
        header('Location:index.php?add=User Added');
    }
    else{
        header('Location:index.php?addError=Something Went Wrong');
    }
}