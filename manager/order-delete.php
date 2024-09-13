<?php global $conn;
session_start();
include "../connector.php";
include "../functions.php";


if (isset($_GET["order_id"])) {
    $order_id = $_GET["order_id"];

    $sql = "DELETE FROM orders WHERE order_id = '$order_id'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();


    header("location: ./order-dashboard.php");
    exit();
}
