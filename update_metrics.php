<?php
// Database connection
include('./config.php');

header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get the POST data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        throw new Exception("Invalid JSON data received");
    }
    
    $type = $data['type'] ?? '';
    $metric = $data['metric'] ?? 0;
    
    // Validate input
    if (!in_array($type, ['generated','excel','csv','pdf'])) {
        throw new Exception("Invalid type specified");
    }
    
        // Insert new record
        $insertStmt = $pdo->prepare("INSERT INTO report_metrics (metrics, type, last_updates) VALUES (?, ?, NOW())");
        $insertStmt->execute([$metric, $type]);
        $message = "New metric record created";

    
    echo json_encode(["status" => "success", "message" => $message]);
    
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>