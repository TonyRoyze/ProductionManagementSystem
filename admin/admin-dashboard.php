<?php global $conn;
session_start();
include "../connector.php";
include "../functions.php";

$user_data = checkLogin($conn);

$monthly_sales = [
    'Jan' => 6000,
    'Feb' => 8000,
    'Mar' => 4000,
    'Apr' => 7000,
    'May' => 9000,
    'Jun' => 5000
];

// Find the maximum value to calculate percentages
$max_sales = max($monthly_sales);

// Generate the bars for monthly sales
foreach ($monthly_sales as $month => $sales) {
    $height = ($sales / $max_sales) * 100;
    echo "<div class='bar' style='height: {$height}%;' title='{$month}: \${$sales}'>";
    echo "<span class='bar-label'>{$month}</span>";
    echo "</div>";
}
?>
?>

<?php include "./admin-header.php"; ?>

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
    <div class="dashboard admin">
    <!-- ... existing card elements ... -->

    <!-- Add new chart containers -->
    <div class="chart-container">
        <canvas id="salesChart"></canvas>
    </div>
    <div class="chart-container">
        <canvas id="usersChart"></canvas>
    </div>
    </div>

    <!-- Add Chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Add custom chart script -->
    <script>
        // Sales Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Monthly Sales',
                    data: [12, 19, 3, 5, 2, 3],
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Monthly Sales Chart'
                    }
                }
            }
        });

        // Users Chart
        const usersCtx = document.getElementById('usersChart').getContext('2d');
        new Chart(usersCtx, {
            type: 'bar',
            data: {
                labels: ['New', 'Active', 'Inactive'],
                datasets: [{
                    label: 'User Statistics',
                    data: [300, 500, 100],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 99, 132, 0.6)'
                    ],
                    borderColor: [
                        'rgb(75, 192, 192)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 99, 132)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'User Statistics'
                    }
                }
            }
        });
    </script>
</body>
