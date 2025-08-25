<?php
include('./config.php');?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Church Asset Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar {
            transition: all 0.3s ease;
            z-index: 60;
            /* Increased z-index to be above backdrop */
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                height: 100vh;
            }

            .sidebar-open .sidebar {
                transform: translateX(0);
            }

            .sidebar-backdrop {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 50;
                /* Lower than sidebar */
            }

            .sidebar-open .sidebar-backdrop {
                display: block;
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body class="bg-gray-50 font-sans">
    <!-- Backdrop for mobile sidebar -->
    <div class="sidebar-backdrop"></div>

    <div class="flex min-h-screen">
        <!-- Sidebar Navigation -->
        <?php include('__includes/sidebar.php'); ?>

        <!-- Main Content -->
        <div class="flex-1 ml-0 md:ml-64 transition-all duration-300">
            <!-- Top Navigation Bar-->
            <?php include('__includes/topbar.php'); ?>

            <!-- Main Content Area -->
            <main>
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    <!-- Welcome Banner -->
                    <div class="bg-blue-50 rounded-lg p-4 sm:p-6 mb-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-400 text-2xl"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium text-blue-800">Welcome to Church Asset Manager</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>Manage all your church equipment and assets in one place. Track locations,
                                        maintenance schedules, and generate reports.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mb-8">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- Add New Asset -->
                            <div class="bg-white overflow-hidden shadow rounded-lg">
                                <div class="p-4 sm:p-5">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                                            <i class="fas fa-plus-circle text-blue-600 text-xl"></i>
                                        </div>
                                        <div class="ml-5 w-0 flex-1">
                                            <h3 class="text-sm font-medium text-gray-900">Add New Asset</h3>
                                            <p class="mt-1 text-sm text-gray-500">Register new church equipment</p>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent 
                                        text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 
                                        focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                            onclick="window.location.href='./add_asset.php'">
                                            Create Asset
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Scan QR/Barcode -->
                            <div class="bg-white overflow-hidden shadow rounded-lg">
                                <div class="p-4 sm:p-5">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                                            <i class="fas fa-qrcode text-green-600 text-xl"></i>
                                        </div>
                                        <div class="ml-5 w-0 flex-1">
                                            <h3 class="text-sm font-medium text-gray-900">Scan Asset</h3>
                                            <p class="mt-1 text-sm text-gray-500">Use QR/barcode scanner</p>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent 
                                        text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700
                                         focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                            onclick="window.location.href='./scanner.php'">
                                            Open Scanner
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Generate Report -->
                            <div class="bg-white overflow-hidden shadow rounded-lg">
                                <div class="p-4 sm:p-5">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                                            <i class="fas fa-file-alt text-purple-600 text-xl"></i>
                                        </div>
                                        <div class="ml-5 w-0 flex-1">
                                            <h3 class="text-sm font-medium text-gray-900">Generate Report</h3>
                                            <p class="mt-1 text-sm text-gray-500">Export asset data</p>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <button type="button" class="inline-flex items-center px-3 py-2 border 
                                        border-transparent text-sm leading-4 font-medium rounded-md shadow-sm 
                                        text-white bg-purple-600 hover:bg-purple-700 focus:outline-none 
                                        focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                                            onclick="window.location.href='./generate-report.php'">
                                            Create Report
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dashboard Content -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Asset Summary -->
                        <div class="lg:col-span-2">
                            <div class="bg-white shadow rounded-lg">
                                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Asset Overview</h3>
                                </div>
                                <div class="p-4 sm:p-6">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                                        <div class="bg-blue-50 p-4 rounded-lg">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 bg-blue-100 rounded-md p-2">
                                                    <i class="fas fa-box text-blue-600"></i>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium text-gray-500">Total Assets</p>

                                                    <?php
                                                        // Get total assets count
                                                        try {
                                                            $totalAssets = $pdo->query("SELECT COUNT(*) FROM assets")->fetchColumn();
                                                        } catch (PDOException $e) {
                                                            error_log("Error fetching total assets count: " . $e->getMessage());
                                                            $totalAssets = 0; // Default value if there's an error
                                                        }
                                                        ?>

                                                    <p class="text-2xl font-semibold text-gray-900">
                                                        <?php echo htmlspecialchars($totalAssets); ?>
                                                    </p>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-green-50 p-4 rounded-lg">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 bg-green-100 rounded-md p-2">
                                                    <i class="fas fa-check-circle text-green-600"></i>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium text-gray-500">Scanned Assets</p>
                                                    <?php
                                                        // Get total assets count
                                                        try {
                                                            $totalScannedAssets = $pdo->query("SELECT COUNT(*) FROM scanned_assets")->fetchColumn();
                                                        } catch (PDOException $e) {
                                                            error_log("Error fetching total assets count: " . $e->getMessage());
                                                            $totalScannedAssets = 0; // Default value if there's an error
                                                        }
                                                        ?>
                                                    <p class="text-2xl font-semibold text-gray-900">
                                                        <?php echo htmlspecialchars($totalScannedAssets); ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // First query to get all distinct asset types
    $typeQuery = "SELECT DISTINCT asset_type FROM assets";
    $typeStmt = $pdo->query($typeQuery);
    
    $defaultCategories = [];
    
    if ($typeStmt->rowCount() > 0) {
        while ($row = $typeStmt->fetch(PDO::FETCH_ASSOC)) {
            $defaultCategories[] = $row['asset_type'];
        }
    } else {
        // Fallback to hardcoded categories if no types exist
        $defaultCategories = ['Sound', 'Furniture', 'Kitchen', 'Office', 'Cleaning', 'Other'];
    }
    
    // Now query to count assets by type
    $countQuery = "SELECT asset_type, COUNT(*) as count FROM assets GROUP BY asset_type";
    $countStmt = $pdo->query($countQuery);
    
    $assetCounts = array_fill(0, count($defaultCategories), 0); // Initialize all counts to 0
    
    if ($countStmt->rowCount() > 0) {
        while ($row = $countStmt->fetch(PDO::FETCH_ASSOC)) {
            $index = array_search($row['asset_type'], $defaultCategories);
            if ($index !== false) {
                $assetCounts[$index] = $row['count'];
            }
        }
    }
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    // Fallback values if connection fails
    $defaultCategories = ['Sound', 'Furniture', 'Kitchen', 'Office', 'Cleaning', 'Other'];
    $assetCounts = [0, 0, 0, 0, 0, 0];
}

// Connection is automatically closed when PDO object is destroyed
?>
                                    <!-- Asset Chart -->
                                    <div class="h-64">
                                        <canvas id="assetChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div>
                            <div class="bg-white shadow rounded-lg">
                                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Activity</h3>
                                </div>
                                <div class="p-4 sm:p-6">
                                    <?php

                                        // Fetch recent activity data ordered by last_updates
                                        $stmt = $pdo->prepare("
                                            SELECT asset_id, content, last_updates
                                            FROM recent_activity
                                            ORDER BY last_updates DESC
                                            LIMIT 6
                                        ");
                                        $stmt->execute();
                                        $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    include('./utilities/time_ago.php');
                                    ?>

                                    <ul class="divide-y divide-gray-200">
                                        <?php if (empty($activities)): ?>
                                        <li class="py-3 text-center text-sm text-gray-500">
                                            No recent activity to display.
                                        </li>
                                        <?php else: ?>
                                        <?php foreach ($activities as $activity): ?>
                                        <?php
                                                    $icon = 'fas fa-check';
                                                    $bg = 'bg-green-100';
                                                    $text = 'text-green-600';

                                                    if (str_contains(strtolower($activity['content']), 'added')) {
                                                        $icon = 'fas fa-plus';
                                                        $bg = 'bg-blue-100';
                                                        $text = 'text-blue-600';
                                                    } elseif (str_contains(strtolower($activity['content']), 'report')) {
                                                        $icon = 'fas fa-file-export';
                                                        $bg = 'bg-purple-100';
                                                        $text = 'text-purple-600';
                                                    }
                                                ?>
                                        <li class="py-3">
                                            <div class="flex items-center space-x-4">
                                                <div class="flex-shrink-0">
                                                    <div
                                                        class="h-8 w-8 rounded-full <?= $bg ?> <?= $text ?> flex items-center justify-center">
                                                        <i class="<?= $icon ?>"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-light text-gray-900 truncate">
                                                        <?= htmlspecialchars($activity['content']) ?>
                                                    </p>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500">
                                                        <?= time_elapsed_string($activity['last_updates']) ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </li>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>

                                    <div class="mt-4">
                                        <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-500">View
                                            all activity <span aria-hidden="true">&rarr;</span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Asset Locations -->
                    <div class="mt-6">
                        <div class="bg-white shadow rounded-lg">
                            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Assets by Location</h3>
                            </div>
                            <div class="p-4 sm:p-6">

                                <?php
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to get all distinct locations and count of assets in each
    $locationQuery = "SELECT location, COUNT(*) as item_count FROM assets GROUP BY location ORDER BY item_count DESC";
    $locationStmt = $pdo->query($locationQuery);
    
    $locations = [];
    
    if ($locationStmt->rowCount() > 0) {
        while ($row = $locationStmt->fetch(PDO::FETCH_ASSOC)) {
            $locations[] = [
                'name' => $row['location'],
                'count' => $row['item_count']
            ];
        }
    } else {
        // Fallback if no locations exist
        $locations = [
            ['name' => 'Sanctuary', 'count' => 0],
            ['name' => 'Office', 'count' => 0],
            ['name' => 'Storage', 'count' => 0]
        ];
    }
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    // Fallback values if connection fails
    $locations = [
        ['name' => 'Sanctuary', 'count' => 0],
        ['name' => 'Office', 'count' => 0],
        ['name' => 'Storage', 'count' => 0]
    ];
}
?>

                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <?php foreach ($locations as $location): ?>
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <h4 class="font-medium text-gray-900">
                                                <?= htmlspecialchars($location['name']) ?>
                                            </h4>
                                            <span
                                                class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                                <?= $location['count'] ?> item
                                                <?= $location['count'] != 1 ? 's' : '' ?>
                                            </span>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Mobile sidebar toggle
        const sidebarToggle = document.querySelector('.sidebar-toggle');
        const sidebarClose = document.querySelector('.sidebar-close');
        const sidebarBackdrop = document.querySelector('.sidebar-backdrop');

        function toggleSidebar() {
            document.body.classList.toggle('sidebar-open');
        }

        function closeSidebar() {
            document.body.classList.remove('sidebar-open');
        }

        sidebarToggle.addEventListener('click', toggleSidebar);
        sidebarClose.addEventListener('click', closeSidebar);
        sidebarBackdrop.addEventListener('click', closeSidebar);

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function (event) {
            const isClickInsideSidebar = document.querySelector('.sidebar').contains(event.target);
            const isClickOnToggleButton = sidebarToggle.contains(event.target);

            if (!isClickInsideSidebar && !isClickOnToggleButton && window.innerWidth <= 768) {
                closeSidebar();
            }
        });


        // Close sidebar when window is resized to desktop size
        window.addEventListener('resize', function () {
            if (window.innerWidth > 768) {
                closeSidebar();
            }
        });



        // Asset Chart (using Chart.js) - Fixed version with top spacing
 // Asset Chart (using Chart.js) - Fixed version with top spacing
const ctx = document.getElementById('assetChart').getContext('2d');
const assetChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($defaultCategories); ?>,
        datasets: [{
            label: 'Assets by Category',
            data: <?php echo json_encode($assetCounts); ?>,
            backgroundColor: [
                'rgba(59, 130, 246, 0.7)',
                'rgba(16, 185, 129, 0.7)',
                'rgba(245, 158, 11, 0.7)',
                'rgba(139, 92, 246, 0.7)',
                'rgba(20, 184, 166, 0.7)',
                'rgba(99, 102, 241, 0.7)'
            ].slice(0, <?php echo count($defaultCategories); ?>),
            borderColor: [
                'rgba(59, 130, 246, 1)',
                'rgba(16, 185, 129, 1)',
                'rgba(245, 158, 11, 1)',
                'rgba(139, 92, 246, 1)',
                'rgba(20, 184, 166, 1)',
                'rgba(99, 102, 241, 1)'
            ].slice(0, <?php echo count($defaultCategories); ?>),
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: {
            padding: {
                top: 20  // Add padding at the top
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                // Method 1: Add a grace percentage (recommended)
                grace: '10%',  // Adds 10% extra space above the highest bar
                
                // Method 2: Alternative - set a specific max value
                // max: Math.max(...<?php echo json_encode($assetCounts); ?>) * 1.2,
                
                // Method 3: Alternative - use suggestedMax
                // suggestedMax: Math.max(...<?php echo json_encode($assetCounts); ?>) * 1.15,
                
                ticks: {
                    // Optional: Add some padding to tick marks
                    padding: 5
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
}); 
    </script>
</body>

</html>