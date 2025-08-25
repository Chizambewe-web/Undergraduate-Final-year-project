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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
          <!-- Page Header -->
          <div class="mb-6 flex justify-between items-center">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Generate Reports</h1>
              <p class="mt-1 text-sm text-gray-600">Create and export detailed reports about your church
                assets</p>
            </div>
            <div class="flex items-center space-x-3">
              <!-- <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                <i class="fas fa-circle text-green-500 mr-1" style="font-size: 6px;"></i>
                Synced
              </span> -->
            </div>
          </div>

          <!-- Report Statistics Section -->
          <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
              <div class="flex flex-col gap-4">
                <!-- Title and Controls Section - Now inline on larger screens -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                  <!-- Title Section -->
                  <h3 class="text-lg leading-6 font-medium text-gray-900">Report Statistics</h3>

                  <!-- Controls Section -->
                  <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full sm:w-auto">
                    <div class="w-full sm:w-auto relative">
                      <select id="date-range" name="date-range" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Date Range</option>
                        <option value="7">Last 7 Days</option>
                        <option value="30">Last 30 Days</option>
                        <option value="90">Last Quarter</option>
                        <option value="365">Last Year</option>
                        <option value="custom">Custom Range</option>
                      </select>

                      <!-- Custom Range Dropdown -->
                      <div id="custom-range-container" class="hidden absolute top-full left-0 mt-2 p-4 bg-white rounded-lg border border-gray-200 shadow-lg z-10 min-w-full">
                        <div class="flex flex-col gap-4">
                          <div>
                            <label for="start-date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                            <input type="date" id="start-date" class="w-full p-2 border border-gray-300 rounded-md">
                          </div>
                          <div>
                            <label for="end-date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                            <input type="date" id="end-date" class="w-full p-2 border border-gray-300 rounded-md">
                          </div>
                          <div class="flex gap-2">
                            <button id="apply-custom-range" type="button" class="flex-1 px-3 py-2 bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                              Apply
                            </button>
                            <button id="cancel-range" type="button" class="flex-1 px-3 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                              Cancel
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>

                    <button id="export-btn" type="button" class="inline-flex items-center justify-center w-full sm:w-auto px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 whitespace-nowrap">
                      <i class="fas fa-file-export mr-2"></i> Report Export
                    </button>
                  </div>
                </div>
              </div>
              <div id="status-message" class="hidden mt-2 p-3 rounded-md"></div>

            </div>


            <script>
              document.addEventListener('DOMContentLoaded', function () {
                const dateRangeSelect = document.getElementById('date-range');
                const exportBtn = document.getElementById('export-btn');
                const customRangeContainer = document.getElementById('custom-range-container');
                const startDateInput = document.getElementById('start-date');
                const endDateInput = document.getElementById('end-date');
                const applyCustomRangeBtn = document.getElementById('apply-custom-range');
                const cancelRangeBtn = document.getElementById('cancel-range');
                const statusMessage = document.getElementById('status-message');

                // Set default end date to today
                const today = new Date();
                endDateInput.value = formatDate(today);

                // Set default start date to 7 days ago
                const sevenDaysAgo = new Date();
                sevenDaysAgo.setDate(today.getDate() - 7);
                startDateInput.value = formatDate(sevenDaysAgo);

                // Track if custom range is being used
                let usingCustomRange = false;

                // Handle date range selection
                dateRangeSelect.addEventListener('change', function () {
                  if (this.value === 'custom') {
                    customRangeContainer.classList.remove('hidden');
                    usingCustomRange = true;
                  } else {
                    customRangeContainer.classList.add('hidden');
                    usingCustomRange = false;
                    updateDateRange(this.value);
                  }
                });

                // Apply custom range
                applyCustomRangeBtn.addEventListener('click', function () {
                  if (!startDateInput.value || !endDateInput.value) {
                    showStatus('Please select both start and end dates.', 'error');
                    return;
                  }

                  if (new Date(startDateInput.value) > new Date(endDateInput.value)) {
                    showStatus('Start date cannot be after end date.', 'error');
                    return;
                  }

                  customRangeContainer.classList.add('hidden');
                  showStatus('Custom date range applied.', 'success');

                  // Set the dropdown to show "Custom Range"
                  dateRangeSelect.value = 'custom';
                  usingCustomRange = true;
                });

                // Cancel custom range selection
                cancelRangeBtn.addEventListener('click', function () {
                  customRangeContainer.classList.add('hidden');
                  dateRangeSelect.value = '';
                  usingCustomRange = false;
                });

                // Handle export button click
                exportBtn.addEventListener('click', function () {
                  let startDate, endDate;

                  if (usingCustomRange) {
                    if (!startDateInput.value || !endDateInput.value) {
                      showStatus('Please select both start and end dates for custom range.', 'error');
                      return;
                    }
                    startDate = startDateInput.value;
                    endDate = endDateInput.value;
                  } else if (dateRangeSelect.value) {
                    const dateRange = getDateRange(parseInt(dateRangeSelect.value));
                    startDate = dateRange.start;
                    endDate = dateRange.end;
                  } else {
                    showStatus('Please select a date range first.', 'error');
                    return;
                  }

                  // Update button text with selected dates
                  const originalText = this.innerHTML;
                  this.innerHTML = `<i class="fas fa-file-export mr-2"></i> Loading...`;
                  this.disabled = true;

                  // In a real application, you would export with these dates
                  console.log(`Exporting from ${startDate} to ${endDate}`);

                  // For demonstration - show success message
                  showStatus(`Exporting data from ${formatDisplayDate(startDate)} to ${formatDisplayDate(endDate)}`, 'success');

                  // Reset button after a short delay (in real app, this would be after the export completes)
                  setTimeout(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                  }, 2000);

                  // In your actual code, you would redirect to the export URL:
                  window.location.href = `report-export.php?start_date=${startDate}&end_date=${endDate}`;
                });

                // Function to update date range based on selection
                function updateDateRange(days) {
                  const dateRange = getDateRange(parseInt(days));
                  // In a real application, you would update the report data here
                  showStatus(`Date range updated to last ${days} days.`, 'success');
                }

                // Function to calculate date range
                function getDateRange(days) {
                  const end = new Date();
                  const start = new Date();
                  start.setDate(end.getDate() - days);

                  return {
                    start: formatDate(start),
                    end: formatDate(end)
                  };
                }

                // Function to format date as YYYY-MM-DD
                function formatDate(date) {
                  const year = date.getFullYear();
                  const month = String(date.getMonth() + 1).padStart(2, '0');
                  const day = String(date.getDate()).padStart(2, '0');
                  return `${year}-${month}-${day}`;
                }

                // Function to format date for display (MM/DD/YYYY)
                function formatDisplayDate(dateString) {
                  const date = new Date(dateString);
                  const month = String(date.getMonth() + 1).padStart(2, '0');
                  const day = String(date.getDate()).padStart(2, '0');
                  const year = date.getFullYear();
                  return `${month}/${day}/${year}`;
                }

                // Function to show status messages
                function showStatus(message, type) {
                  statusMessage.textContent = message;
                  statusMessage.className = 'mt-2 p-3 rounded-md';

                  if (type === 'error') {
                    statusMessage.classList.add('bg-red-100', 'text-red-700');
                  } else {
                    statusMessage.classList.add('bg-green-100', 'text-green-700');
                  }

                  statusMessage.classList.remove('hidden');

                  // Auto-hide success messages after 5 seconds
                  if (type === 'success') {
                    setTimeout(() => {
                      statusMessage.classList.add('hidden');
                    }, 5000);
                  }
                }
              });
            </script>
            <div class="px-4 py-5 sm:p-6">

                    <!-- Report Statistics -->
 <div class="-mt-7">
<?php

try {
    
    // Query to get counts for each file type
    $sql = "SELECT type, COUNT(*) as count FROM report_metrics GROUP BY type";
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Initialize counts
    $excelCount = 0;
    $csvCount = 0;
    $pdfCount = 0;
    
    // Process results
    foreach ($results as $row) {
        switch ($row['type']) {
            case 'excel':
                $excelCount = $row['count'];
                break;
            case 'csv':
                $csvCount = $row['count'];
                break;
            case 'pdf':
                $pdfCount = $row['count'];
                break;
        }
    }
    
    // Output the counts (you can use these variables in your HTML)
    // echo "Excel: " . $excelCount . "\n";
    // echo "CSV: " . $csvCount . "\n";
    // echo "PDF: " . $pdfCount . "\n";
    
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-100 rounded-md p-2">
                                <i class="fas fa-file-excel text-green-600"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-500">Export Excel</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $excelCount; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-100 rounded-md p-2">
                                <i class="fas fa-file-csv text-blue-600"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-500">Export CSV</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $csvCount; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-100 rounded-md p-2">
                                <i class="fas fa-file-pdf text-red-600"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-500">Export PDF</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $pdfCount; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report Chart -->
                <div class="mt-6 h-64">
                    <canvas id="reportChart"></canvas>
                </div>
            </div>
    </div>
<?php
// Fetch data for the current year, grouped by month and type
$currentYear = date('Y');
$sql = "SELECT 
            MONTH(last_updates) as month, 
            type, 
            COUNT(*) as count 
        FROM report_metrics 
        WHERE YEAR(last_updates) = :year 
        GROUP BY MONTH(last_updates), type 
        ORDER BY month, type";

$stmt = $pdo->prepare($sql);
$stmt->execute([':year' => $currentYear]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize data structure
$monthlyData = [
    'pdf' => array_fill(1, 12, 0),
    'excel' => array_fill(1, 12, 0),
    'csv' => array_fill(1, 12, 0)
];

// Process the results
foreach ($results as $row) {
    $month = (int)$row['month'];
    $type = $row['type'];
    $count = (int)$row['count'];
    
    if (array_key_exists($type, $monthlyData)) {
        $monthlyData[$type][$month] = $count;
    }
}
?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the canvas element
        const ctx = document.getElementById('reportChart').getContext('2d');
        
        // Create the chart with data from PHP
        const reportChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    {
                        label: 'Export Excel',
                        data: <?php echo json_encode(array_values($monthlyData['excel'])); ?>,
                        backgroundColor: 'rgba(34, 197, 94, 0.6)',
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Export CSV',
                        data: <?php echo json_encode(array_values($monthlyData['csv'])); ?>,
                        backgroundColor: 'rgba(59, 130, 246, 0.6)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Export PDF',
                        data: <?php echo json_encode(array_values($monthlyData['pdf'])); ?>,
                        backgroundColor: 'rgba(239, 68, 68, 0.6)',
                        borderColor: 'rgba(239, 68, 68, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grace: '10%', // Adds 10% padding above the highest bar
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
    </script>

            </div>
      </main>


      <!-- JavaScript for Advanced Filters Toggle -->
      <script>
        document.addEventListener('DOMContentLoaded', function () {
          const toggleButton = document.querySelector('[aria-label="Advanced Filters"]');
          const advancedFilters = document.getElementById('advanced-filters');

          toggleButton.addEventListener('click', function () {
            advancedFilters.classList.toggle('hidden');
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-chevron-down');
            icon.classList.toggle('fa-chevron-up');
          });
        });
      </script>
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
  </script>



</body>

</html>