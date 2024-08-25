<?php global $conn;
session_start();
include "../connector.php";
include "../functions.php";
$user_data = checkLogin($conn);
?>
<?php include "./manager-header.php"; ?>

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
            <a class='btn-animate bg-default' href='order-create.php'>
              <div class='sign'><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus">
                  <path d="M5 12h14"/><path d="M12 5v14"/></svg></div>
              <div class='text'>Create</div>
            </a>
            </div>
        <table class="table">
            <thead>
            <tr>
                <th>Part Name</th>
                <th>Quantity</th>
                <th>Workstation Name</th>
                <th>Order Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $sql =
                "SELECT order_id, part_name, quantity, user_name, order_status " .
                "FROM orders JOIN user ON orders.workstation_id = user.workstation_id " .
                "JOIN part ON orders.part_id = part.part_id";
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
                        <td>" .
                    ($row["order_status"] == 0
                        ? "Not Accepted"
                        : ($row["order_status"] == 1
                            ? "In Progress"
                            : "Shipped")) .
                    "</td>" .
                    ($row["order_status"] == 0
                        ? "<td class='action'>
                                <a class='btn-edit bg-default' href='order-edit.php?order_id=$row[order_id]'>
                                    <div class='sign'><svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='3' stroke-linecap='round' stroke-linejoin='round' class='lucide lucide-pencil'>
                                        <path d='M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z'/>
                                        <path d='m15 5 4 4'/></svg>
                                    </div>
                                </a>
                                <a class='btn-delete bg-red' href='order-delete.php?order_id=$row[order_id]'>
                                    <div class='sign'><svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='3' stroke-linecap='round' stroke-linejoin='round' class='lucide lucide-trash-2'>
                                    <path d='M3 6h18'/><path d='M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6'/><path d='M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2'/><line x1='10' x2='10' y1='11' y2='17'/><line x1='14' x2='14' y1='11' y2='17'/></svg>
                                    </div>
                                </a>
                        </td>"
                        : "<td></td>") .
                    "</tr>";
            }
            ?>
            </tbody>
            </table>
        </div>

    </body>
