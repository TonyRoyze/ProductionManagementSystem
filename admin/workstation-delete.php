<?php global $conn;
session_start();
include "../connector.php";
include "../functions.php";

$user_data = checkLogin($conn);

if (isset($_GET["workstaion_id"])) {
    $workstation_id = $_GET["workstaion_id"];

    $sql = "DELETE FROM workstation WHERE workstation_id = '$workstation_id'";
    $conn->query($sql);

    $sql = "DELETE FROM user WHERE workstation_id = '$workstation_id'";
    $conn->query($sql);

    header("location: ./workstation-dashboard.php");
    exit();
}
