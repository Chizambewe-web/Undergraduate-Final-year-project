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
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                height: 100vh;
                width: 80%;
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
            }

            .sidebar-open .sidebar-backdrop {
                display: block;
            }

            .main-content {
                margin-left: 0;
            }

            /* Improved table scrolling */
            .table-container {
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                /* Smooth scrolling on iOS */
            }

            /* Prevent text wrapping in table cells */
            .asset-table {
                min-width: 700px;
                /* Minimum width to ensure all columns are visible */
                width: 100%;
            }

            /* Better spacing for mobile */
            .stats-grid {
                grid-template-columns: repeat(1, 1fr);
            }

            /* Adjust padding for mobile */
            .mobile-px {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }

        /* Better touch targets */
        button,
        a {
            touch-action: manipulation;
        }

        /* Table cell styling */
        .table-cell {
            white-space: nowrap;
            padding: 1rem 1.5rem;
        }

        /* Add shadow to indicate scrollable area */
        .table-container {
            position: relative;
        }

        .table-container:after {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            width: 30px;
            height: 100%;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.9) 100%);
            pointer-events: none;
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
                <?php
                    // Get counts for stats cards
                    try {
                        $totalAssets = $pdo->query("SELECT COUNT(*) FROM assets")->fetchColumn();
                        $activeAssets = $pdo->query("SELECT COUNT(*) FROM assets WHERE is_active = 1")->fetchColumn();
                        $maintenanceNeeded = $pdo->query("SELECT COUNT(*) FROM assets WHERE condition_status = 'needs-repair'")->fetchColumn();
                        $outOfService = $pdo->query("SELECT COUNT(*) FROM assets WHERE is_active = 0")->fetchColumn();
                    } catch (PDOException $e) {
                        // Handle error appropriately
                        error_log("Error fetching stats: " . $e->getMessage());
                        $totalAssets = $activeAssets = $maintenanceNeeded = $outOfService = 0;
                    }

                    // Pagination variables
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $perPage = 10;
                    $offset = ($page - 1) * $perPage;

                    // Get all assets for the table with pagination
                    try {
                        $stmt = $pdo->prepare("SELECT `id`, `asset_name`, `asset_type`, `serial_number`, `location`, `condition_status`, `estimated_value`, `description`, `image_data`, `barcode`, `qrcode`, `barcode_value`, `qrcode_value`, `date_acquired`, `last_updated`, `is_active` FROM `assets` LIMIT :limit OFFSET :offset");
                        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
                        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                        $stmt->execute();
                        $assets = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        // Get total count for pagination
                        $totalAssetsCount = $pdo->query("SELECT COUNT(*) FROM assets")->fetchColumn();
                        $totalPages = ceil($totalAssetsCount / $perPage);
                    } catch (PDOException $e) {
                        error_log("Error fetching assets: " . $e->getMessage());
                        $assets = [];
                        $totalPages = 1;
                    }
                    ?>

                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 mobile-px">
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6 stats-grid">
                        <!-- Total Assets -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                                        <i class="fas fa-boxes text-blue-600 text-xl"></i>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Assets</dt>
                                        <dd class="flex items-baseline">
                                            <p class="text-2xl font-semibold text-gray-900">
                                                <?php echo htmlspecialchars($totalAssets); ?>
                                            </p>
                                        </dd>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Active Assets -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dt class="text-sm font-medium text-gray-500 truncate">In Service</dt>
                                        <dd class="flex items-baseline">
                                            <p class="text-2xl font-semibold text-gray-900">
                                                <?php echo htmlspecialchars($activeAssets); ?>
                                            </p>
                                        </dd>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Maintenance Needed -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                                        <i class="fas fa-tools text-yellow-600 text-xl"></i>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dt class="text-sm font-medium text-gray-500 truncate">Needs Repair</dt>
                                        <dd class="flex items-baseline">
                                            <p class="text-2xl font-semibold text-gray-900">
                                                <?php echo htmlspecialchars($maintenanceNeeded); ?>
                                            </p>
                                        </dd>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Out of Service -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dt class="text-sm font-medium text-gray-500 truncate">Out of Service</dt>
                                        <dd class="flex items-baseline">
                                            <p class="text-2xl font-semibold text-gray-900">
                                                <?php echo htmlspecialchars($outOfService); ?>
                                            </p>
                                        </dd>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Asset Table -->
                    <div class="bg-white shadow rounded-lg overflow-hidden">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Asset Inventory</h3>
                                <div class="mt-3 md:mt-0">
                                    <div class="relative rounded-md shadow-sm">
                                        <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium 
        rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 
        focus:ring-offset-2 focus:ring-blue-500" onclick="window.location.href='./add_asset.php'">
                                            Add Asset
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="table-container">
                            <table class="asset-table min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider table-cell">
                                            Name
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider table-cell">
                                            Category
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider table-cell">
                                            Location
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider table-cell">
                                            Condition
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider table-cell">
                                            Last Updated
                                        </th>
                                        <th scope="col" class="relative px-6 py-3 table-cell">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($assets as $asset): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="table-cell">
                                            <div class="flex items-center">
                                                <div
                                                    class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-md flex items-center justify-center">
                                                    <?php if (!empty($asset['image_data'])): ?>
                                                    <img src="<?php echo htmlspecialchars($asset['image_data']); ?>"
                                                        class="h-10 w-10 rounded-md"
                                                        alt="<?php echo htmlspecialchars($asset['asset_name']); ?>" />
                                                    <?php else: ?>
                                                    <i class="fas fa-box text-blue-600"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <?php echo htmlspecialchars($asset['asset_name']); ?>
                                                    </div>
                                                    <div class="text-sm text-gray-500">#
                                                        <?php echo htmlspecialchars($asset['serial_number']); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="table-cell">
                                            <div class="text-sm text-gray-900">
                                                <?php echo htmlspecialchars($asset['asset_type']); ?>
                                            </div>
                                        </td>
                                        <td class="table-cell">
                                            <div class="text-sm text-gray-900">
                                                <?php echo htmlspecialchars($asset['location']); ?>
                                            </div>
                                        </td>
                                        <td class="table-cell">
                                            <?php 
                $statusClass = 'bg-gray-100 text-gray-800';
                $statusText = ucwords(str_replace('-', ' ', $asset['condition_status']));
                
                // Set different colors based on condition status
                switch ($asset['condition_status']) {
                    case 'excellent':
                        $statusClass = 'bg-green-100 text-green-800';
                        break;
                    case 'good':
                        $statusClass = 'bg-blue-100 text-blue-800';
                        break;
                    case 'fair':
                        $statusClass = 'bg-amber-100 text-amber-800';
                        break;
                    case 'poor':
                        $statusClass = 'bg-orange-100 text-orange-800';
                        break;
                    case 'needs-repair':
                        $statusClass = 'bg-yellow-100 text-yellow-800';
                        break;
                    default:
                        $statusClass = 'bg-gray-100 text-gray-800';
                }
                
                // If asset is not active, override with out of service status
                if (!$asset['is_active']) {
                    $statusClass = 'bg-red-100 text-red-800';
                    $statusText = 'Out of Service';
                }
                ?>
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusClass; ?>">
                                                <?php echo htmlspecialchars($statusText); ?>
                                            </span>
                                        </td>
                                        <td class="table-cell text-sm text-gray-500">
                                            <?php 
                $lastUpdated = new DateTime($asset['last_updated']);
                echo htmlspecialchars($lastUpdated->format('M j, Y'));
                ?>
                                        </td>
                                        <td class="table-cell text-right text-sm font-medium">
                                            <button
                                                onclick="document.getElementById('modal-<?php echo $asset['id']; ?>').classList.remove('hidden')"
                                                class="text-blue-600 hover:text-blue-900 mr-3">
                                                View Codes
                                            </button>
                                            <a href="edit_asset.php?id=<?php echo (int)$asset['id']; ?>"
                                                class="text-gray-600 hover:text-gray-900">Edit</a>
                                        </td>
                                    </tr>

                                    <!-- Modal for each asset -->
                                    <div id="modal-<?php echo $asset['id']; ?>"
                                        class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
                                        <!-- Modal Container - Increased width to 120 (w-120) -->
                                        <div
                                            class="relative top-20 mx-auto p-5 border w-[600px] shadow-lg rounded-md bg-white">
                                            <!-- Modal Content -->
                                            <div class="mt-3 text-center">
                                                <!-- Modal Header -->
                                                <div class="flex justify-between items-center pb-3">
                                                    <h3 class="text-xl font-medium text-gray-900">
                                                        <?php echo htmlspecialchars($asset['asset_name']); ?> Codes
                                                    </h3>
                                                    <button
                                                        onclick="document.getElementById('modal-<?php echo $asset['id']; ?>').classList.add('hidden')"
                                                        class="text-gray-400 hover:text-gray-500">
                                                        <span class="text-2xl">&times;</span>
                                                    </button>
                                                </div>

                                                <!-- Modal Body -->
                                                <div class="mt-2 px-4 py-3">
                                                    <div class="grid grid-cols-1 gap-6 m-4">
                                                        <div class="bg-white p-4 rounded-lg shadow relative">
                                                            <div class="flex justify-between items-center mb-2">
                                                                <h4 class="font-medium text-gray-700">Barcode</h4>
                                                                <?php if (!empty($asset['barcode'])): ?>
                                                                <a href="<?php echo htmlspecialchars($asset['barcode']); ?>"
                                                                    download="barcode-<?php echo htmlspecialchars($asset['asset_name']); ?>.png"
                                                                    class="text-blue-500 hover:text-blue-700 transition-colors duration-200"
                                                                    title="Download Barcode">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                                                        stroke="currentColor">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                                    </svg>
                                                                </a>
                                                                <?php endif; ?>
                                                            </div>
                                                            <?php if (!empty($asset['barcode'])): ?>
                                                            <img src="<?php echo htmlspecialchars($asset['barcode']); ?>"
                                                                class="mx-auto h-32" alt="Barcode" />
                                                            <?php else: ?>
                                                            <p class="text-gray-500">No barcode available</p>
                                                            <?php endif; ?>
                                                            <div class="mt-2 text-sm text-gray-600">
                                                                <span class="font-medium">Value:</span>
                                                                <?php echo htmlspecialchars($asset['barcode_value'] ?? 'N/A'); ?>
                                                            </div>
                                                        </div>

                                                        <div class="bg-white p-4 rounded-lg shadow relative">
                                                            <div class="flex justify-between items-center mb-2">
                                                                <h4 class="font-medium text-gray-700">QR Code</h4>
                                                                <?php if (!empty($asset['qrcode'])): ?>
                                                                <a href="<?php echo htmlspecialchars($asset['qrcode']); ?>"
                                                                    download="qrcode-<?php echo htmlspecialchars($asset['asset_name']); ?>.png"
                                                                    class="text-blue-500 hover:text-blue-700 transition-colors duration-200"
                                                                    title="Download QR Code">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                                                        stroke="currentColor">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                                    </svg>
                                                                </a>
                                                                <?php endif; ?>
                                                            </div>
                                                            <?php if (!empty($asset['qrcode'])): ?>
                                                            <img src="<?php echo htmlspecialchars($asset['qrcode']); ?>"
                                                                class="mx-auto h-32" alt="QR Code" />
                                                            <?php else: ?>
                                                            <p class="text-gray-500">No QR code available</p>
                                                            <?php endif; ?>
                                                            <div class="mt-2 text-sm text-gray-600">
                                                                <span class="font-medium">Value:</span>
                                                                <?php echo htmlspecialchars($asset['qrcode_value'] ?? 'N/A'); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Modal Footer -->
                                                <div class="flex justify-end pt-4 border-t">
                                                    <button
                                                        onclick="document.getElementById('modal-<?php echo $asset['id']; ?>').classList.add('hidden')"
                                                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 mr-2">
                                                        Close
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    <?php if (empty($assets)): ?>
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No assets found
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                            <script>
                                // Close modals when clicking outside of them
                                document.querySelectorAll('[id^="modal-"]').forEach(modal => {
                                    modal.addEventListener('click', function (e) {
                                        if (e.target === this) {
                                            this.classList.add('hidden');
                                        }
                                    });
                                });
                            </script>
                        </div>
                        <div
                            class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <a href="?page=<?php echo $page > 1 ? $page - 1 : 1; ?>"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Previous
                                </a>
                                <a href="?page=<?php echo $page < $totalPages ? $page + 1 : $totalPages; ?>"
                                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Next
                                </a>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Showing
                                        <span class="font-medium">
                                            <?php echo ($offset + 1); ?>
                                        </span>
                                        to
                                        <span class="font-medium">
                                            <?php echo min($offset + $perPage, $totalAssetsCount); ?>
                                        </span>
                                        of
                                        <span class="font-medium">
                                            <?php echo $totalAssetsCount; ?>
                                        </span>
                                        results
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex shadow-sm -space-x-px" aria-label="Pagination">
                                        <a href="?page=<?php echo $page > 1 ? $page - 1 : 1; ?>"
                                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Previous</span>
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                        <?php for ($i = 1; $i <= min(5, $totalPages); $i++): ?>
                                        <a href="?page=<?php echo $i; ?>"
                                            class="<?php echo $i === $page ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'; ?> relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                            <?php echo $i; ?>
                                        </a>
                                        <?php endfor; ?>
                                        <?php if ($totalPages > 5): ?>
                                        <span
                                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                            ...
                                        </span>
                                        <a href="?page=<?php echo $totalPages; ?>"
                                            class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                            <?php echo $totalPages; ?>
                                        </a>
                                        <?php endif; ?>
                                        <a href="?page=<?php echo $page < $totalPages ? $page + 1 : $totalPages; ?>"
                                            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Next</span>
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </nav>
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

        // Prevent zooming on double-tap for mobile
        document.addEventListener('dblclick', function (e) {
            e.preventDefault();
        }, { passive: false });

        // Better touch handling for buttons
        document.querySelectorAll('button, a').forEach(element => {
            element.addEventListener('touchstart', function () {
                this.classList.add('active');
            });
            element.addEventListener('touchend', function () {
                this.classList.remove('active');
            });
        });
    </script>
</body>

</html>