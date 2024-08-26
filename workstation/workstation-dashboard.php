<?php global $conn;
session_start();
include "../connector.php";
include "../functions.php";
$user_data = checkLogin($conn);

if (isset($_POST["order_id"])) {
    $order_id = $_POST["order_id"];
    $current_status = $_POST["order_status"];

    $new_status = ($current_status + 1) % 4;

    $sql = "UPDATE orders SET order_status = $new_status WHERE order_id = $order_id";
    if ($conn->query($sql) === false) {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<?php include "./workstation-header.php"; ?>

<body>
    <div class='navigation'>
        <div class='container'>
                <?php include "./nav.php"; ?>
        </div>
        <a class='btn-animate bg-red' href='../logout.php'>
          <div class='sign'><svg viewBox='0 0 512 512'><path d='M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z'></path></svg></div>
          <div class='text'>Logout</div>
        </a>
    </div>
    <div class="dashboard">
        <div class="table-name">
            <h1>Order Details</h1>
        </div>
        <table class="table">
            <thead>
            <tr>
                <th>Part Name</th>
                <th>Quantity</th>
                <th>WorkStation</th>
                <th>Order Status</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $sql =
                "SELECT order_id, part_name, quantity, user_name, order_status " .
                "FROM orders JOIN user ON orders.workstation_id = user.workstation_id " .
                "JOIN part ON orders.part_id = part.part_id " .
                "WHERE order_status < 4";

            $result = $conn->query($sql);

            if (!$result) {
                die("Invalid query" . $conn->connect_error);
            }

            while ($row = $result->fetch_assoc()) {
                echo "
                    <tr>
                        <td>{$row["part_name"]}</td>
                        <td>{$row["quantity"]}</td>
                        <td>{$row["user_name"]}</td>
                        <td>
                            <form method='post'>
                                <input type='hidden' name='order_id' value='{$row["order_id"]}'>
                                <input type='hidden' name='order_status' value='{$row["order_status"]}'>
                                <button type='submit' class='btn-status " .
                    ($row["order_status"] == 0
                        ? "red"
                        : ($row["order_status"] == 1
                            ? "yellow"
                            : ($row["order_status"] == 2
                                ? "green"
                                : "blue"))) .
                    "'>" .
                    ($row["order_status"] == 0
                        ? "Not Accepted"
                        : ($row["order_status"] == 1
                            ? "In Progress"
                            : ($row["order_status"] == 2
                                ? "Shipped"
                                : "Complete"))) .
                    "</button>
                            </form>
                        </td>
                    </tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</body>
