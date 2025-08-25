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
            z-index: 60; /* Increased z-index to be above backdrop */
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
                z-index: 50; /* Lower than sidebar */
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
                    <!-- Page Header -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
                            <p class="mt-1 text-sm text-gray-500">Register new church equipment or property</p>
                        </div>
                        <div>
                            <button type="button"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-arrow-left mr-2"></i> Back to Assets
                            </button>
                        </div>
                    </div>

                    <!-- Settings Cards Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- Church Information Card -->
                        <div class="bg-white overflow-hidden shadow rounded-lg settings-card">
                            

<form method="POST" action="">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
            <i class="fas fa-church text-blue-500 mr-2"></i> Church Information
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">Basic details about your church</p>
    </div>
    <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
        <?php
        // Initialize alert variables
        $alert_type = '';
        $alert_message = '';
        $show_alert = false;
        
        // Fetch current church info
        $current_info = [];
        try {
            $stmt = $pdo->query("SELECT * FROM church_info ORDER BY id DESC LIMIT 1");
            $current_info = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Table might not exist or be empty
            $current_info = false;
        }
        
        // Check if form was submitted
        if(isset($_POST["submit"])) {
            // Get form data
            $name = $_POST["church-name"];
            $description = $_POST["church-address"];
            $phone_no = $_POST["church-phone"];
            
            // Validate input data
            if(empty($name) || empty($description) || empty($phone_no)) {
                $alert_type = 'error';
                $alert_message = 'All fields are required!';
                $show_alert = true;
            } else {
                // Check if we're updating or inserting
                if($current_info && isset($current_info['id'])) {
                    // Update existing record
                    $sql = "UPDATE church_info SET name = :name, description = :description, phone_no = :phone_no, last_updated = CURRENT_TIMESTAMP WHERE id = :id";
                } else {
                    // Insert new record
                    $sql = "INSERT INTO church_info (name, description, phone_no) VALUES (:name, :description, :phone_no)";
                }
                
                try {
                    $stmt = $pdo->prepare($sql);
                    
                    // Bind parameters
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':description', $description);
                    $stmt->bindParam(':phone_no', $phone_no);
                    
                    if($current_info && isset($current_info['id'])) {
                        $stmt->bindParam(':id', $current_info['id']);
                    }
                    
                    // Execute the query
                    $stmt->execute();
                    
                    // Refetch the updated info
                    $stmt = $pdo->query("SELECT * FROM church_info ORDER BY id DESC LIMIT 1");
                    $current_info = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Set success alert
                    $alert_type = 'success';
                    $alert_message = 'Church information updated successfully!';
                    $show_alert = true;
                    
                } catch (PDOException $e) {
                    // Set error alert
                    $alert_type = 'error';
                    $alert_message = 'Error updating information: ' . $e->getMessage();
                    $show_alert = true;
                }
            }
        }
        ?>

        <?php if ($show_alert): ?>
        <div class="flex items-center p-4 mb-4 text-sm <?php 
                echo $alert_type === 'success' 
                    ? 'text-green-800 bg-green-50' 
                    : 'text-red-800 bg-red-50';
            ?> rounded-lg" id="myAlert" role="alert">
            <svg class="shrink-0 inline w-4 h-4 me-3" aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path
                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
            </svg>
            <span class="sr-only">Info</span>
            <div>
                <span class="font-medium">
                    <?php echo $alert_type === 'success' ? 'Success!' : 'Error!'; ?>
                </span>
                <?php echo htmlspecialchars($alert_message); ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="space-y-4">
            <div>
                <label for="church-name" class="block text-sm font-medium text-gray-700">Church Name</label>
                <input type="text" id="church-name" name="church-name" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                    value="<?php echo $current_info ? htmlspecialchars($current_info['name']) : 'Grace Community Church'; ?>">
            </div>
            <div>
                <label for="church-address" class="block text-sm font-medium text-gray-700">Address</label>
                <textarea id="church-address" name="church-address" rows="2" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"><?php echo $current_info ? htmlspecialchars($current_info['description']) : '123 Main Street, Anytown, AN 12345'; ?></textarea>
            </div>
            <div>
                <label for="church-phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input type="tel" id="church-phone" name="church-phone" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                    value="<?php echo $current_info ? htmlspecialchars($current_info['phone_no']) : '(555) 123-4567'; ?>">
            </div>
        </div>

        <button type="submit" name="submit" class="inline-flex items-center mt-4 px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i class="fas fa-save mr-2"></i> Apply Changes
        </button>
    </div>
</form>
                        </div>

                        <!-- System Preferences Card -->
                        <div class="bg-white overflow-hidden shadow rounded-lg settings-card">
                            <div class="px-4 py-5 sm:px-6">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
                                    <i class="fas fa-cog text-blue-500 mr-2"></i> System Preferences
                                </h3>
                                <p class="mt-1 max-w-2xl text-sm text-gray-500">Configure how the system behaves</p>
                            </div>
                            <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-700">Auto-backup System Data</h4>
                                            <p class="text-sm text-gray-500">Automatic backups</p>
                                        </div>
                                        <label for="auto-backup" class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" id="auto-backup" class="sr-only peer" checked disabled>
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                    <div>
                                        <label for="date-format" class="block text-sm font-medium text-gray-700">Date Format</label>
                                        <select id="date-format" name="date-format" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                            <option value="yyyy-mm-dd">YYYY-MM-DD</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="time-zone" class="block text-sm font-medium text-gray-700">Time Zone</label>
                                        <select id="time-zone" name="time-zone" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                            <option value="cst">Central Time (CT)</option>
                                        </select>
                                    </div>
                                                                        <button type="submit" name="submit" class="inline-flex items-center mt-4 px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i class="fas fa-save mr-2"></i> Apply Changes (Disable)
        </button>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Danger Zone -->
                    <div class="bg-white shadow rounded-lg border border-red-200 settings-section">
                        <div class="px-4 py-5 sm:px-6 border-b border-red-200 bg-red-50">
                            <h3 class="text-lg leading-6 font-medium text-red-800 flex items-center">
                                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i> Danger Zone
                            </h3>
                            <p class="mt-1 text-sm text-red-600">Irreversible and destructive actions</p>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <div class="flex flex-col md:flex-row md:items-center justify-between">
                                <div class="mb-4 md:mb-0">
                                    <h4 class="text-sm font-medium text-gray-900">Delete all data</h4>
                                    <p class="text-sm text-gray-500">Permanently remove all assets and data from the system</p>
                                </div>
                                <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <i class="fas fa-trash-alt mr-2"></i> Delete All Data
                                </button>
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
        document.addEventListener('click', function(event) {
            const isClickInsideSidebar = document.querySelector('.sidebar').contains(event.target);
            const isClickOnToggleButton = sidebarToggle.contains(event.target);
            
            if (!isClickInsideSidebar && !isClickOnToggleButton && window.innerWidth <= 768) {
                closeSidebar();
            }
        });

        // Close sidebar when window is resized to desktop size
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                closeSidebar();
            }
        });
    </script>
</body>
</html>