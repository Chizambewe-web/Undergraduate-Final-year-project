<?php 
include('./config.php');?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        .asset-table {
            font-size: 0.875rem;
        }
        .asset-table th {
            background-color: #4F46E5;
            color: white;
        }
        .export-btn {
            transition: all 0.3s ease;
        }
        .export-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        @media print {
            body * {
                visibility: hidden;
            }
            .asset-table, .asset-table * {
                visibility: visible;
            }
            .asset-table {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <header class="bg-white rounded-lg shadow-md p-6 mb-8 relative">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Asset Management System</h1>
            <p class="text-gray-600">View and export your asset inventory data</p>
            
            <!-- Go Back Home Button -->
            <a href="./generate-report.php" class="absolute top-6 right-6 bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200 ease-in-out transform hover:-translate-y-0.5 focus:ring-2 focus:ring-blue-300 focus:ring-opacity-50 focus:outline-none">
                <i class="fas fa-home mr-2"></i>Go Back
            </a>
        </header>
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 md:mb-0">Asset Inventory</h2>
<div class="flex flex-wrap gap-3">
    <button id="exportExcel" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md export-btn flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        Export Excel
    </button>
    <button id="exportCSV" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md export-btn flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        Export CSV
    </button>
    <button id="exportPDF" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md export-btn flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        Export PDF
    </button>
</div>
            </div>

<script>
    // Add event listeners to each button
    document.getElementById('exportExcel').addEventListener('click', function() {
        trackExport('excel');
    });
    
    document.getElementById('exportCSV').addEventListener('click', function() {
        trackExport('csv');
    });
    
    document.getElementById('exportPDF').addEventListener('click', function() {
        trackExport('pdf');
    });

    function trackExport(type) {
        const payload = {
            metric: 1,
            type: type,
            timestamp: new Date().toISOString()
        };

        fetch('update_metrics.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.text())
        .then(data => {
            console.log('Export tracked successfully:', data);
        })
        .catch(error => {
            console.error('Error tracking export:', error);
        });
    }
</script>

            <div class="overflow-x-auto">
                <table class="w-full asset-table" id="assetTable">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left">ID</th>
                            <th class="px-4 py-3 text-left">Asset Name</th>
                            <th class="px-4 py-3 text-left">Type</th>
                            <th class="px-4 py-3 text-left">Serial No.</th>
                            <th class="px-4 py-3 text-left">Barcode</th>                            
                            <th class="px-4 py-3 text-left">Location</th>
                            <th class="px-4 py-3 text-left">Condition</th>
                            <th class="px-4 py-3 text-left">Estimaed Value</th>
                            <th class="px-4 py-3 text-left">Current Value</th>
                            <th class="px-4 py-3 text-left">Purchase Date</th>
                            <th class="px-4 py-3 text-left">Acquired</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        try {
                            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            
                            // Initialize date filters
                            $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
                            $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
                            
                            // Build the SQL query with optional date filters
                            $sql = "SELECT id, asset_name, asset_type, serial_number, location, condition_status, estimated_value, current_value, purchase_date, barcode, date_acquired 
                                    FROM assets 
                                    WHERE is_active = 1";
                            
                            $params = [];
                            
                            if (!empty($start_date) && !empty($end_date)) {
                                $sql .= " AND date_acquired BETWEEN :start_date AND :end_date";
                                $params[':start_date'] = $start_date;
                                $params[':end_date'] = $end_date;
                            } elseif (!empty($start_date)) {
                                $sql .= " AND date_acquired >= :start_date";
                                $params[':start_date'] = $start_date;
                            } elseif (!empty($end_date)) {
                                $sql .= " AND date_acquired <= :end_date";
                                $params[':end_date'] = $end_date;
                            }
                            
                            $sql .= " ORDER BY date_acquired DESC, id";
                            
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute($params);
                            $assets = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            if (count($assets) > 0) {
                                foreach ($assets as $asset) {
                                    echo "<tr class='hover:bg-gray-50'>";
                                    echo "<td class='px-4 py-3'>" . htmlspecialchars($asset['id']) . "</td>";
                                    echo "<td class='px-4 py-3 font-medium text-gray-800'>" . htmlspecialchars($asset['asset_name']) . "</td>";
                                    echo "<td class='px-4 py-3'>" . htmlspecialchars($asset['asset_type']) . "</td>";
                                    echo "<td class='px-4 py-3'>" . htmlspecialchars($asset['serial_number']) . "</td>";
                                    echo "<td class='px-4 py-3'><img src='" . htmlspecialchars($asset['barcode']) . "' class='mx-auto h-10' alt='Barcode' /></td>";
                                    echo "<td class='px-4 py-3'>" . htmlspecialchars($asset['location']) . "</td>";
                                    echo "<td class='px-4 py-3'><span class='px-2 py-1 text-xs rounded-full " . getStatusClass($asset['condition_status']) . "'>" . htmlspecialchars($asset['condition_status']) . "</span></td>";
                                    echo "<td class='px-4 py-3'>$" . number_format($asset['estimated_value'], 2) . "</td>";
                                    echo "<td class='px-4 py-3'>$" . number_format($asset['current_value'], 2) . "</td>";
                                    echo "<td class='px-4 py-3'>" . htmlspecialchars($asset['purchase_date'], 2) . "</td>";
                                    echo "<td class='px-4 py-3'>" . htmlspecialchars($asset['date_acquired']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9' class='px-4 py-3 text-center text-gray-500'>No assets found for the selected date range</td></tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='9' class='px-4 py-3 text-center text-red-500'>Error fetching data: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                        }

                        // Helper function to get CSS class based on condition status
                        function getStatusClass($status) {
                            switch (strtolower($status)) {
                                case 'excellent':
                                    return 'bg-green-100 text-green-800';
                                case 'good':
                                    return 'bg-blue-100 text-blue-800';
                                case 'fair':
                                    return 'bg-yellow-100 text-yellow-800';
                                case 'poor':
                                    return 'bg-red-100 text-red-800';
                                default:
                                    return 'bg-gray-100 text-gray-800';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Export to Excel
            document.getElementById('exportExcel').addEventListener('click', function() {
                exportToExcel();
            });

            // Export to CSV
            document.getElementById('exportCSV').addEventListener('click', function() {
                exportToCSV();
            });

            // Export to PDF
            document.getElementById('exportPDF').addEventListener('click', function() {
                exportToPDF();
            });

            function exportToExcel() {
                try {
                    // Create a new workbook
                    const wb = XLSX.utils.book_new();
                    
                    // Get table data
                    const table = document.querySelector('.asset-table');
                    const ws = XLSX.utils.table_to_sheet(table);
                    
                    // Add worksheet to workbook
                    XLSX.utils.book_append_sheet(wb, ws, "Assets");
                    
                    // Generate Excel file and trigger download
                    XLSX.writeFile(wb, "asset_data.xlsx");
                    
                    showNotification('Excel file downloaded successfully!', 'green');
                } catch (error) {
                    console.error('Error exporting to Excel:', error);
                    showNotification('Error exporting to Excel. Please try again.', 'red');
                }
            }

            function exportToCSV() {
                try {
                    // Get table data
                    const table = document.querySelector('.asset-table');
                    const rows = table.querySelectorAll('tr');
                    let csvContent = "";
                    
                    // Process header row
                    const headerCells = rows[0].querySelectorAll('th');
                    const headerRow = Array.from(headerCells).map(cell => `"${cell.textContent.trim()}"`).join(',');
                    csvContent += headerRow + '\r\n';
                    
                    // Process data rows
                    for (let i = 1; i < rows.length; i++) {
                        const cells = rows[i].querySelectorAll('td');
                        const row = Array.from(cells).map(cell => `"${cell.textContent.trim()}"`).join(',');
                        csvContent += row + '\r\n';
                    }
                    
                    // Create download link
                    const encodedUri = encodeURI('data:text/csv;charset=utf-8,' + csvContent);
                    const link = document.createElement('a');
                    link.setAttribute('href', encodedUri);
                    link.setAttribute('download', 'asset_data.csv');
                    document.body.appendChild(link);
                    
                    // Trigger download
                    link.click();
                    document.body.removeChild(link);
                    
                    showNotification('CSV file downloaded successfully!', 'green');
                } catch (error) {
                    console.error('Error exporting to CSV:', error);
                    showNotification('Error exporting to CSV. Please try again.', 'red');
                }
            }

            function exportToPDF() {
                try {
                    // Show loading notification
                    showNotification('Generating PDF...', 'blue');
                    
                    // Get the table element
                    const table = document.getElementById('assetTable');
                    
                    // Create a temporary container for PDF generation
                    const pdfContainer = document.createElement('div');
                    pdfContainer.style.width = '100%';
                    pdfContainer.style.padding = '20px';
                    pdfContainer.style.backgroundColor = 'white';
                    pdfContainer.style.position = 'absolute';
                    pdfContainer.style.left = '-9999px';
                    
                    // Clone the table
                    const tableClone = table.cloneNode(true);
                    pdfContainer.appendChild(tableClone);
                    
                    // Add title
                    const title = document.createElement('h2');
                    title.textContent = 'Asset Inventory Report';
                    title.style.fontSize = '20px';
                    title.style.fontWeight = 'bold';
                    title.style.marginBottom = '15px';
                    title.style.textAlign = 'center';
                    pdfContainer.insertBefore(title, tableClone);
                    
                    // Add date
                    const date = document.createElement('div');
                    date.textContent = 'Generated on: ' + new Date().toLocaleDateString();
                    date.style.textAlign = 'right';
                    date.style.marginBottom = '15px';
                    pdfContainer.insertBefore(date, title.nextSibling);
                    
                    document.body.appendChild(pdfContainer);
                    
                    // Use html2canvas to capture the content
                    html2canvas(pdfContainer, {
                        scale: 2, // Higher quality
                        useCORS: true,
                        logging: false
                    }).then(canvas => {
                        // Remove the temporary container
                        document.body.removeChild(pdfContainer);
                        
                        // Create PDF
                        const { jsPDF } = window.jspdf;
                        const doc = new jsPDF('landscape', 'mm', 'a4');
                        
                        const imgData = canvas.toDataURL('image/jpeg', 1.0);
                        const imgWidth = doc.internal.pageSize.getWidth();
                        const pageHeight = doc.internal.pageSize.getHeight();
                        const imgHeight = canvas.height * imgWidth / canvas.width;
                        
                        let heightLeft = imgHeight;
                        let position = 0;
                        
                        // Add first page
                        doc.addImage(imgData, 'JPEG', 0, position, imgWidth, imgHeight);
                        heightLeft -= pageHeight;
                        
                        // Add additional pages if needed
                        while (heightLeft >= 0) {
                            position = heightLeft - imgHeight;
                            doc.addPage();
                            doc.addImage(imgData, 'JPEG', 0, position, imgWidth, imgHeight);
                            heightLeft -= pageHeight;
                        }
                        
                        // Save the PDF
                        doc.save('asset_inventory.pdf');
                        
                        showNotification('PDF file downloaded successfully!', 'green');
                    }).catch(error => {
                        console.error('Error generating PDF:', error);
                        showNotification('Error generating PDF. Please try again.', 'red');
                    });
                    
                } catch (error) {
                    console.error('Error exporting to PDF:', error);
                    showNotification('Error exporting to PDF. Please try again.', 'red');
                }
            }

            function showNotification(message, color) {
                // Remove any existing notifications first
                const existingNotifications = document.querySelectorAll('.export-notification');
                existingNotifications.forEach(notification => notification.remove());
                
                // Create notification element
                const notification = document.createElement('div');
                notification.className = `export-notification fixed bottom-4 right-4 px-4 py-3 rounded-md shadow-md text-white font-semibold ${color === 'green' ? 'bg-green-600' : color === 'red' ? 'bg-red-600' : 'bg-blue-600'}`;
                notification.textContent = message;
                notification.style.zIndex = '1000';
                
                // Add to page
                document.body.appendChild(notification);
                
                // Remove after 3 seconds
                setTimeout(() => {
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 3000);
            }
        });
    </script>
</body>
</html>