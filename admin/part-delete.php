<?php global $conn;
session_start();
include "../connector.php";
include "../functions.php";

$user_data = checkLogin($conn);

if (isset($_GET["part_id"])) {
    $part_id = $_GET["part_id"];
    
    // Check for workstations using the part
    $checkSql = "SELECT COUNT(*) as count FROM workstation WHERE part_id = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("i", $part_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row["count"] > 0) {
        echo "<script>alert('Cannot delete part because it is being used by workstations.'); window.location.href = 'part-dashboard.php';</script>";
    } else {
        $deleteSql = "DELETE FROM part WHERE part_id = ?";
        $stmt = $conn->prepare($deleteSql);
        $stmt->bind_param("i", $part_id);
        if ($stmt->execute()) {
            echo "<script>alert('Part deleted successfully.'); window.location.href = 'part-dashboard.php';</script>";
        } else {
            echo "<script>alert('Error deleting part: " . $conn->error . "'); window.location.href = 'part-dashboard.php';</script>";
        }
    }
}
