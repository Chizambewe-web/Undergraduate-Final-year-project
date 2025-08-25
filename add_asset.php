<?php 
include('./config.php');
include('./processors/create_asset.php');?>
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
                    <!-- Page Header -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Add New Asset</h1>
                            <p class="mt-1 text-sm text-gray-500">Register new church equipment or property</p>
                        </div>
                        <div>
                            <button type="button"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-arrow-left mr-2"></i> Back to Assets
                            </button>
                        </div>
                    </div>

                    <!-- Form Section -->
                    <div class="bg-white shadow rounded-lg overflow-hidden">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Asset Information</h3>
                            <p class="mt-1 text-sm text-gray-500">Fill in the details of the new asset.</p>
                        </div>
                        <div class="px-4 py-5 sm:p-6">

                            <?php if ($show_alert): ?>
                            <div class="flex items-center p-4 mb-4 text-sm <?php 
                                    echo $alert_type === 'success' 
                                        ? 'text-green-800 bg-green-50 light:bg-gray-800 light:text-green-400' 
                                        : 'text-red-800 bg-red-50 light:bg-gray-800 light:text-red-400';
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

                            <form method="POST" action="./add_asset.php" enctype="multipart/form-data">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-4">
                    <!-- Asset Name -->
                    <div>
                        <label for="asset-name" class="block text-sm font-medium text-gray-700">Asset Name *</label>
                        <input type="text" id="asset-name" name="asset-name"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="e.g. Sanctuary Sound System" required>
                    </div>

                    <!-- Asset Type -->
                    <div>
                        <label for="asset-type" class="block text-sm font-medium text-gray-700">Asset Type *</label>
                        <select id="asset-type" name="asset-type"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                            <option value="">Select type</option>
                            <option value="Audio Equipment">Audio Equipment</option>
                            <option value="Video Equipment">Video Equipment</option>
                            <option value="Furniture">Furniture</option>
                            <option value="Kitchen">Kitchen Equipment</option>
                            <option value="Vehicle">Vehicle</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <!-- Serial Number -->
                    <div>
                        <label for="serial-number" class="block text-sm font-medium text-gray-700">Serial Number</label>
                        <input type="text" id="serial-number" name="serial-number"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="Auto-generated" readonly>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            // Generate a random serial number in format: XXXX-XXXXXX-XXX-XXXX
                            function generateSerialNumber() {
                                const chars = 'ACDEFHJKLMNPQRTUVWXY49';

                                // Generate each segment with different lengths
                                const segment1 = generateSegment(chars, 5);    // 5 chars (e.g., FVKPV)
                                const segment2 = generateSegment(chars, 5);    // 5 chars (e.g., VSJ57)
                                const segment3 = generateSegment(chars, 3);    // 3 chars (e.g., PVV)
                                                    const segment4 = generateSegment(chars, 4);    // 4 chars (e.g., SJ57)

                                // Combine segments with dashes
                                return `${segment1}-${segment2}${segment3}-${segment3}-${segment4}`;
                            }

                            // Helper function to generate a random segment
                            function generateSegment(chars, length) {
                                let result = '';
                                for (let i = 0; i < length; i++) {
                                    result += chars.charAt(Math.floor(Math.random() * chars.length));
                                }
                                return result;
                            }

                            // Get the input field and set the generated serial number
                            const serialNumberInput = document.getElementById('serial-number');
                            serialNumberInput.value = generateSerialNumber();
                        });
                    </script>

                    <!-- Location -->
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700">Location *</label>
                        <select id="location" name="location"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                            <option value="">Select location</option>
                            <option value="sanctuary">Sanctuary</option>
                            <option value="Fellowship Hall">Fellowship Hall</option>
                            <option value="kitchen">Kitchen</option>
                            <option value="Classroom 1">Classroom 1</option>
                            <option value="Classroom 2">Classroom 2</option>
                            <option value="Office">Office</option>
                            <option value="Storage">Storage</option>
                        </select>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-4">
                    <!-- Condition -->
                    <div>
                        <label for="condition" class="block text-sm font-medium text-gray-700">Condition *</label>
                        <select id="condition" name="condition"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                            <option value="">Select condition</option>
                            <option value="excellent">Excellent</option>
                            <option value="good">Good</option>
                            <option value="fair">Fair</option>
                            <option value="poor">Poor</option>
                            <option value="needs-repair">Needs Repair</option>
                        </select>
                    </div>

                    <!-- Estimated Value -->
                    <div>
                        <label for="estimated-value" class="block text-sm font-medium text-gray-700">Estimated Value (K)</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">₵</span>
                            </div>
                            <input type="number" name="estimated-value" id="estimated-value"
                                class="block w-full pl-7 pr-12 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                placeholder="0.00" step="0.01" required>
                        </div>
                    </div>

                    <!-- Current Value -->
                    <div>
                        <label for="current-value" class="block text-sm font-medium text-gray-700">Current Value (K)</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">₵</span>
                            </div>
                            <input type="number" name="current-value" id="current-value"
                                class="block w-full pl-7 pr-12 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                placeholder="0.00" step="0.01" required>
                        </div>
                    </div>
                    
                    <!-- Purchased Date -->
                    <div>
                        <label for="purchased-date" class="block text-sm font-medium text-gray-700">Purchased Date</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="date" name="purchased-date" id="purchased-date"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <div class="mt-1">
                    <textarea id="description" name="description" rows="4"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        placeholder="Additional details about the asset (e.g., specifications, maintenance history, special instructions)" required></textarea>
                </div>
                <p class="mt-2 text-sm text-gray-500">Provide a detailed description of the asset including any special features or notes.</p>
            </div>

            <!-- Photo Upload -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700">Asset Photo</label>
                <label for="dropzone-file" class="cursor-pointer">
                    <div id="upload-area" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-blue-500 hover:bg-blue-50 transition-colors duration-200">
                        <div class="space-y-1 text-center" id="upload-prompt">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <span class="relative bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                    Upload a file
                                </span>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                        </div>
                        <div id="preview-container" class="hidden relative">
                            <img id="image-preview" class="max-h-48 max-w-full rounded-md" src="" alt="Preview">
                            <button id="remove-image" type="button"
                                class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center transform translate-x-1/2 -translate-y-1/2 hover:bg-red-600">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                    </div>
                </label>
                <input id="dropzone-file" name="dropzone-file" type="file" class="sr-only" accept="image/*">
            </div>

            <!-- Barcode/QR Code Section (Hidden by default) -->
            <div class="grid sm:grid-cols-2 gap-4 mt-6 hidden">
                <div class="min-h-[100px] bg-gray-100 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-600 mb-2">Barcode</p>
                    <canvas id="barcode-canvas" class="mx-auto"></canvas>
                    <input type="hidden" id="barcode-data" name="barcode-data">
                    <input type="hidden" id="barcode-value" name="barcode-value">
                </div>
                <div class="min-h-[100px] bg-gray-100 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-600 mb-2">QR Code</p>
                    <canvas id="qrcode-canvas" class="mx-auto"></canvas>
                    <input type="hidden" id="qrcode-data" name="qrcode-data">
                    <input type="hidden" id="qrcode-value" name="qrcode-value">
                </div>
            </div>
                                        
            <!-- Form Actions -->
            <div class="mt-8 flex justify-end space-x-3 pt-5 border-t border-gray-200">
                <button type="button"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </button>
                <button type="submit" name="submit"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-save mr-2"></i> Save Asset
                </button>
            </div>
        </form>
                        </div>
                    </div>

                    <!-- Help Section -->
                    <div class="mt-6 bg-blue-50 rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-question-circle text-blue-400 text-2xl"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium text-blue-800">Need help adding an asset?</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>Make sure to include all required fields (marked with *). For equipment with
                                        serial numbers, we recommend including them for better tracking. Contact support
                                        if you need assistance.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>




<!-- Add these scripts to your form -->
<script src="https://cdn.jsdelivr.net/npm/bwip-js@3.0.5/dist/bwip-js.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Generate serial number (your existing code)
    const serialNumberInput = document.getElementById('serial-number');
    const barcodeValueInput = document.getElementById('barcode-value');
    const qrcodeValueInput = document.getElementById('qrcode-value');
    
    // Generate barcode
    function generateBarcode() {
        const serialNumber = serialNumberInput.value;
        if (!serialNumber) return;
        
        try {
            bwipjs.toCanvas('barcode-canvas', {
                bcid: 'code128',
                text: serialNumber,
                scale: 3,
                height: 15,
                includetext: true,
                textxalign: 'center'
            });
            
            // Convert canvas to base64 and update hidden inputs
            const canvas = document.getElementById('barcode-canvas');
            const barcodeData = canvas.toDataURL('image/png');
            document.getElementById('barcode-data').value = barcodeData;
            barcodeValueInput.value = serialNumber;
        } catch (e) {
            console.error('Error generating barcode:', e);
        }
    }
    
    // Generate QR code
    function generateQRCode() {
        const serialNumber = serialNumberInput.value;
        if (!serialNumber) return;
        
        const canvas = document.getElementById('qrcode-canvas');
        QRCode.toCanvas(canvas, serialNumber, {
            width: 128,
            color: {
                dark: '#000',
                light: '#fff'
            }
        }, function(error) {
            if (error) console.error('Error generating QR code:', error);
            
            // Convert canvas to base64 and update hidden inputs
            const qrcodeData = canvas.toDataURL('image/png');
            document.getElementById('qrcode-data').value = qrcodeData;
            qrcodeValueInput.value = serialNumber;
        });
    }
    
    // Generate both when serial number changes
    serialNumberInput.addEventListener('change', function() {
        generateBarcode();
        generateQRCode();
    });
    
    // Generate initially
    generateBarcode();
    generateQRCode();
});
</script>

    <script>
        $(document).ready(function () {
            // When a file is selected
            $('#dropzone-file').change(function (e) {
                if (this.files && this.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        // Show preview and hide upload prompt
                        $('#preview-container').removeClass('hidden');
                        $('#upload-prompt').addClass('hidden');
                        $('#image-preview').attr('src', e.target.result);
                    }

                    reader.readAsDataURL(this.files[0]);
                }
            });

            // When remove button is clicked
            $('#remove-image').click(function (e) {
                e.preventDefault();
                e.stopPropagation(); // Prevent triggering the file input
                $('#dropzone-file').val(''); // Clear the file input
                $('#preview-container').addClass('hidden');
                $('#upload-prompt').removeClass('hidden');
            });

            // Handle drag and drop
            const dropZone = $('.border-dashed');

            // Prevent default drag behaviors
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.on(eventName, function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                });
            });

            // Highlight drop zone when item is dragged over it
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.on(eventName, function () {
                    $(this).addClass('border-blue-500 bg-blue-50');
                });
            });

            // Remove highlight when drag leaves or drops
            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.on(eventName, function () {
                    $(this).removeClass('border-blue-500 bg-blue-50');
                });
            });

            // Handle dropped files
            dropZone.on('drop', function (e) {
                const dt = e.originalEvent.dataTransfer;
                const files = dt.files;

                if (files.length) {
                    $('#dropzone-file')[0].files = files;
                    $('#dropzone-file').trigger('change');
                }
            });
        });
    </script>

  <!-- Alert fadeout -->
  <script>
    $(document).ready(function () {
      setTimeout(function () {
        $('#myAlert').fadeOut('slow', function () {
          $(this).remove();
        });
      }, 3000); // remove alert after 4 seconds
    });
  </script>

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