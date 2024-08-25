<?php global $conn;
session_start();
include "../connector.php";
include "../functions.php";

$user_data = checkLogin($conn);

if (isset($_GET["username"])) {
    $username = $_GET["username"];

    $sql = "DELETE FROM user WHERE user_name = '$username'";
    $conn->query($sql);

    header("location: ./user-dashboard.php");
    exit();
}
