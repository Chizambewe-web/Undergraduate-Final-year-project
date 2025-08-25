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
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.min.js"></script>
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
                    <!-- Scanner Header -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Asset Scanner</h1>
                            <p class="text-sm text-gray-500">Scan QR codes or barcodes to manage church assets</p>
                        </div>
      <button
        type="button"
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium 
        rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 
        focus:ring-offset-2 focus:ring-blue-500"
         onclick="window.location.href='./add_asset.php'"
      >
        Add Asset
      </button>
                    </div>


<!-- Scanner Container -->
<div class="bg-white shadow rounded-lg overflow-hidden max-w-6xl mx-auto flex">
    <!-- Left Column - Scanner Controls -->
    <div class="w-1/2 p-4 border-r border-gray-200">
        <!-- Scanner Status Bar -->
        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 -mt-4 -mx-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <span id="scannerStatus" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        <i class="fas fa-circle text-gray-500 mr-1" style="font-size: 6px;"></i>
                        Scanner Off
                    </span>
                    <span class="ml-3 text-sm text-gray-500">Camera: Back</span>
                </div>
                <div>
                    <button class="text-gray-400 hover:text-gray-500 mr-3">
                        <i class="fas fa-lightbulb"></i>
                    </button>
                    <button class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-camera"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Video Preview and Capture Area -->
        <div class="mt-4">
            <video id="preview" autoplay muted playsinline class="w-full h-auto rounded-lg border border-gray-200 hidden"></video>
            <div class="flex justify-center mt-4">
                <canvas id="canvas" class="hidden"></canvas>
                <img id="capturedImageDisplay" class="max-w-xs max-h-40 border border-gray-200 rounded-lg hidden" alt="Captured image">
            </div>

            <!-- Manual Input Field (Hidden by default) -->
            <div id="manualInputContainer" class="mt-4 hidden">
                <label for="serialNumberInput" class="block text-sm font-medium text-gray-700">Enter Serial Number Manually</label>
                <input type="text" id="serialNumberInput" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Enter serial number...">
                <button id="submitManualBtn" class="mt-2 w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md shadow-sm">
                    Submit Serial Number
                </button>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-4 mt-4">
                <button id="captureBtn" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition hidden">
                    Capture Serial Number
                </button>
                <button id="scanBtn" disabled class="flex-1 bg-gray-400 text-white py-2 px-4 rounded-lg transition">
                    Scan with OCR
                </button>
            </div>
        </div>

        <!-- Scanner Controls -->
        <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 -mx-4 -mb-4">
            <div class="flex items-center justify-center space-x-4">
                <button id="cameraBtn" type="button"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-camera mr-2"></i> Capture
                </button>
                <button id="uploadBtn" type="button"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-image mr-2"></i> Upload Image
                </button>
                <button id="manualBtn" type="button"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-keyboard mr-2"></i> Enter Manually
                </button>
            </div>
        </div>

        <!-- Hidden file input for upload -->
        <input type="file" id="fileInput" accept="image/*" class="hidden">
    </div>

    <!-- Right Column - Results -->
    <div class="w-1/2 p-4">
        <div class="bg-gray-50 rounded-lg border border-gray-200 h-full">
            <div class="p-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Scan Results</h3>
            </div>
            <div id="result" class="p-4 overflow-y-auto" style="height: calc(100% - 56px);">
                <div class="text-center text-gray-500 py-8">
                    <i class="fas fa-barcode text-4xl mb-2"></i>
                    <p>Results will appear here</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/tesseract.js@4/dist/tesseract.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const video = document.getElementById('preview');
    const canvas = document.getElementById('canvas');
    const capturedImageDisplay = document.getElementById('capturedImageDisplay');
    const captureBtn = document.getElementById('captureBtn');
    const scanBtn = document.getElementById('scanBtn');
    const resultDiv = document.getElementById('result');
    const cameraBtn = document.getElementById('cameraBtn');
    const uploadBtn = document.getElementById('uploadBtn');
    const manualBtn = document.getElementById('manualBtn');
    const fileInput = document.getElementById('fileInput');
    const manualInputContainer = document.getElementById('manualInputContainer');
    const serialNumberInput = document.getElementById('serialNumberInput');
    const submitManualBtn = document.getElementById('submitManualBtn');
    const scannerStatus = document.getElementById('scannerStatus');

    let stream = null;
    let capturedImage = null;
    let currentMode = null;

    // Toast notification function
    function showToast(message, type = 'info') {
        const backgroundColor = {
            'success': 'linear-gradient(to right, #00b09b, #96c93d)',
            'error': 'linear-gradient(to right, #ff5f6d, #ffc371)',
            'info': 'linear-gradient(to right, #667eea, #764ba2)',
            'processing': 'linear-gradient(to right, #4facfe, #00f2fe)'
        };

        Toastify({
            text: message,
            duration: type === 'processing' ? -1 : 3000,
            close: true,
            gravity: "top",
            position: "right",
            style: {
                background: backgroundColor[type] || backgroundColor['info'],
            },
            onClick: function(){}
        }).showToast();
    }

    // Hide processing toast
    function hideProcessingToast() {
        const toasts = document.querySelectorAll('.toastify');
        toasts.forEach(toast => {
            if (toast.textContent.includes('Processing')) {
                toast.style.display = 'none';
            }
        });
    }

    function updateResult(content) {
        if (typeof content === 'string') {
            resultDiv.innerHTML = `<div class="p-4">${content}</div>`;
        } else {
            resultDiv.innerHTML = content;
        }
    }

    function formatSerialNumber(serialNumber) {
        const parts = serialNumber.split(/[-\/:]/);
        return parts.length > 1 ? parts.join('-') : serialNumber;
    }

    // Database integration function
    async function processSerialNumber(serialNumber) {
        try {
            showToast('Processing asset scan...', 'processing');
            
            const response = await fetch('./processors/process_scan.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    serial_number: serialNumber
                })
            });

            const result = await response.json();
            hideProcessingToast();

            if (result.success) {
                showToast('Asset successfully scanned and added!', 'success');
                
                // Display asset information
                updateResult(`
                    <div class="p-4">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                                <div>
                                    <h4 class="font-medium text-green-800">Asset Found & Scanned!</h4>
                                    <p class="text-green-600 text-sm">Serial: ${serialNumber}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-lg mb-3">Asset Details:</h4>
                            <div class="space-y-2 text-sm">
                                <div><span class="font-medium">Name:</span> ${result.asset.asset_name}</div>
                                <div><span class="font-medium">Type:</span> ${result.asset.asset_type}</div>
                                <div><span class="font-medium">Location:</span> ${result.asset.location}</div>
                                <div><span class="font-medium">Condition:</span> ${result.asset.condition_status}</div>
                                <div><span class="font-medium">Value:</span> $${result.asset.estimated_value}</div>
                            </div>
                            
                            <button onclick="copyToClipboard('${serialNumber}')" class="mt-3 text-sm bg-blue-100 hover:bg-blue-200 text-blue-800 py-2 px-3 rounded">
                                <i class="fas fa-copy mr-1"></i> Copy Serial Number
                            </button>
                        </div>
                    </div>
                `);

                // Refresh page after 3 seconds
                setTimeout(() => {
                    location.reload();
                }, 3000);

            } else {
                showToast(result.message || 'Asset not found in database', 'error');
                updateResult(`
                    <div class="p-4">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-3"></i>
                                <div>
                                    <h4 class="font-medium text-red-800">Asset Not Found</h4>
                                    <p class="text-red-600 text-sm">Serial: ${serialNumber}</p>
                                    <p class="text-red-600 text-sm mt-1">${result.message}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
            }

        } catch (error) {
            console.error('Error processing serial number:', error);
            hideProcessingToast();
            showToast('Error processing scan. Please try again.', 'error');
            updateResult(`
                <div class="p-4">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                            <div>
                                <h4 class="font-medium text-red-800">Processing Error</h4>
                                <p class="text-red-600 text-sm">Please check your connection and try again.</p>
                            </div>
                        </div>
                    </div>
                </div>
            `);
        }
    }

    async function initCamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
            video.srcObject = stream;
            video.classList.remove('hidden');
            captureBtn.classList.remove('hidden');
            scannerStatus.innerHTML = '<i class="fas fa-circle text-blue-500 mr-1" style="font-size: 6px;"></i> Camera On';
            scannerStatus.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800';
        } catch (error) {
            console.error("Camera init failed", error);
            updateResult(`<p class="text-red-600">Error: ${error.message}</p>`);
        }
    }

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        video.classList.add('hidden');
        captureBtn.classList.add('hidden');
    }

    captureBtn.addEventListener('click', () => {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        capturedImage = canvas.toDataURL('image/png');
        capturedImageDisplay.src = capturedImage;
        capturedImageDisplay.classList.remove('hidden');
        updateResult("Image captured. Click 'Scan with OCR' to process.");
        scanBtn.disabled = false;
        scanBtn.classList.remove('bg-gray-400');
        scanBtn.classList.add('bg-green-500', 'hover:bg-green-600');
        scannerStatus.innerHTML = '<i class="fas fa-circle text-green-500 mr-1" style="font-size: 6px;"></i> Image Ready';
        scannerStatus.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800';
    });

    function setMode(mode) {
        currentMode = mode;
        capturedImageDisplay.classList.add('hidden');
        manualInputContainer.classList.add('hidden');
        scanBtn.disabled = true;
        scanBtn.classList.remove('bg-green-500', 'hover:bg-green-600');
        scanBtn.classList.add('bg-gray-400');

        switch (mode) {
            case 'camera':
                stopCamera();
                initCamera();
                updateResult("Camera starting...");
                break;
            case 'upload':
                stopCamera();
                fileInput.click();
                updateResult("Select an image file to upload");
                scannerStatus.innerHTML = '<i class="fas fa-circle text-blue-500 mr-1" style="font-size: 6px;"></i> Upload Mode';
                scannerStatus.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800';
                break;
            case 'manual':
                stopCamera();
                manualInputContainer.classList.remove('hidden');
                serialNumberInput.value = '';
                serialNumberInput.focus();
                updateResult("Enter the serial number manually and click Submit");
                scannerStatus.innerHTML = '<i class="fas fa-circle text-purple-500 mr-1" style="font-size: 6px;"></i> Manual Mode';
                scannerStatus.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800';
                break;
        }
    }

    // Fixed manual submission function
    async function processManualInput() {
        const manualText = serialNumberInput.value.trim();
        if (!manualText) {
            updateResult("<div class='p-4 text-red-600'>Please enter a serial number</div>");
            return;
        }

        const formattedSerial = formatSerialNumber(manualText);
        
        // Process with database
        await processSerialNumber(formattedSerial);
        
        // Update scanner status
        scannerStatus.innerHTML = '<i class="fas fa-circle text-green-500 mr-1" style="font-size: 6px;"></i> Processing Complete';
        scannerStatus.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800';
    }

    scanBtn.addEventListener('click', async () => {
        if (!capturedImage && currentMode !== 'manual') {
            updateResult("No image captured yet.");
            return;
        }

        updateResult("<div class='text-center py-4'><i class='fas fa-spinner fa-spin text-blue-500 text-2xl mb-2'></i><p>Processing OCR...</p></div>");
        scanBtn.disabled = true;
        scanBtn.classList.remove('bg-green-500', 'hover:bg-green-600');
        scanBtn.classList.add('bg-gray-400');

        try {
            const { data: { text } } = await Tesseract.recognize(
                capturedImage,
                'eng',
                {
                    logger: m => console.log(m),
                    tessedit_char_whitelist: '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-/:'
                }
            );

            const matches = text.match(/([A-Z0-9]{3,}[-\/:][A-Z0-9]{3,}|[A-Z0-9]{6,})/gi);
            if (matches?.length) {
                const unique = [...new Set(matches.filter(x => x.length >= 6))];
                const combined = unique.map(x => formatSerialNumber(x)).join('-');
                
                // Process with database
                await processSerialNumber(combined);
            } else {
                updateResult(`<div class="p-4 text-gray-700"><strong>No serial numbers detected.</strong><br><pre>${text}</pre></div>`);
            }
        } catch (err) {
            console.error(err);
            updateResult(`<div class="p-4 text-red-600">Error: ${err.message}</div>`);
        } finally {
            scanBtn.disabled = false;
            scanBtn.classList.remove('bg-gray-400');
            scanBtn.classList.add('bg-green-500', 'hover:bg-green-600');
        }
    });

    fileInput.addEventListener('change', function (e) {
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function (event) {
                capturedImage = event.target.result;
                capturedImageDisplay.src = capturedImage;
                capturedImageDisplay.classList.remove('hidden');
                updateResult("Image uploaded. Click 'Scan with OCR' to process.");
                scanBtn.disabled = false;
                scanBtn.classList.remove('bg-gray-400');
                scanBtn.classList.add('bg-green-500', 'hover:bg-green-600');
                scannerStatus.innerHTML = '<i class="fas fa-circle text-green-500 mr-1" style="font-size: 6px;"></i> Image Ready';
                scannerStatus.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800';
            };
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    // Event listeners for manual input
    submitManualBtn.addEventListener('click', processManualInput);
    
    // Allow Enter key to submit manual input
    serialNumberInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            processManualInput();
        }
    });

    cameraBtn.addEventListener('click', () => setMode('camera'));
    uploadBtn.addEventListener('click', () => setMode('upload'));
    manualBtn.addEventListener('click', () => setMode('manual'));

    window.addEventListener('beforeunload', stopCamera);
    updateResult(`<div class="text-center text-gray-500 py-8"><i class="fas fa-barcode text-4xl mb-2"></i><p>Click "Capture" to start camera or "Upload Image" to select a file</p></div>`);
});

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => alert("Copied to clipboard: " + text)).catch(console.error);
}
</script>

                    <!-- Recently Scanned Assets -->
                    <div class="mt-8">
                        <div class="bg-white shadow rounded-lg overflow-hidden">
                            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Recently Scanned Assets</h3>
                            </div>
                            <div class="px-4 py-5 sm:p-6">
                                <div class="overflow-x-auto">
<?php
include('./utilities/time_ago.php');
    // Fetch scanned assets with asset details
    $stmt = $pdo->prepare("
        SELECT 
            a.id,
            a.asset_name,
            a.location,
            a.condition_status,
            sa.last_updated
        FROM scanned_assets sa
        INNER JOIN assets a ON sa.asset_id = a.id
        ORDER BY sa.last_updated DESC
    ");
    $stmt->execute();
    $assets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Tailwind Table -->
<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Scanned</th>
            <th class="px-6 py-3 relative"><span class="sr-only">Actions</span></th>
        </tr>
    </thead>
<tbody class="bg-white divide-y divide-gray-200">
    <?php if (empty($assets)): ?>
        <tr>
            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                No scanned assets found.
            </td>
        </tr>
    <?php else: ?>
        <?php foreach ($assets as $asset): ?>
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    CH-00<?= htmlspecialchars($asset['id']) ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <?= htmlspecialchars($asset['asset_name']) ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <?= htmlspecialchars($asset['location']) ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <?php
                        $status = $asset['condition_status'];
                        $statusClass = match($status) {
                            'Good' => 'bg-green-100 text-green-800',
                            'Needs Service' => 'bg-yellow-100 text-yellow-800',
                            'Damaged' => 'bg-red-100 text-red-800',
                            default => 'bg-gray-100 text-gray-800'
                        };
                    ?>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
                        <?= htmlspecialchars($status) ?>
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <?= time_elapsed_string($asset['last_updated']) ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="#" class="text-blue-600 hover:text-blue-900">View</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</tbody>

</table>



                                </div>
                                <div class="mt-4">
                                    <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-500">View all
                                        scanned assets <span aria-hidden="true">&rarr;</span></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white shadow rounded-lg p-4 flex items-center">
                            <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                                <i class="fas fa-search text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-900">Search Assets</h3>
                                <p class="mt-1 text-sm text-gray-500">Find assets by name or ID</p>
                            </div>
                        </div>
                        <div class="bg-white shadow rounded-lg p-4 flex items-center">
                            <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                                <i class="fas fa-barcode text-green-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-900">Generate Labels</h3>
                                <p class="mt-1 text-sm text-gray-500">Print QR/barcode labels</p>
                            </div>
                        </div>
                        <div class="bg-white shadow rounded-lg p-4 flex items-center">
                            <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                                <i class="fas fa-clipboard-check text-purple-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-900">Start Inventory</h3>
                                <p class="mt-1 text-sm text-gray-500">Begin full asset audit</p>
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