<?php
// Enable error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

// Set header JSON
header('Content-Type: application/json');

// Log untuk debugging
error_log("=== CHECK STATUS REQUEST ===");
error_log("GET params: " . print_r($_GET, true));

// Hanya terima GET request dengan parameter order_id
if (!isset($_GET['order_id'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Order ID tidak ditemukan dalam parameter',
        'debug' => 'Parameter order_id kosong'
    ]);
    exit;
}

$order_id = $conn->real_escape_string($_GET['order_id']);

error_log("Searching for Order ID: " . $order_id);

// Query untuk cari transaksi berdasarkan order_id
$sql = "SELECT * FROM transactions WHERE order_id = '$order_id'";
error_log("SQL Query: " . $sql);

$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        'success' => false,
        'message' => 'Database query error',
        'error' => $conn->error
    ]);
    exit;
}

error_log("Query result rows: " . $result->num_rows);

if ($result->num_rows > 0) {
    $transaction = $result->fetch_assoc();
    
    error_log("Transaction found: " . print_r($transaction, true));
    
    echo json_encode([
        'success' => true,
        'data' => $transaction
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Transaksi tidak ditemukan',
        'debug' => [
            'order_id' => $order_id,
            'query' => $sql
        ]
    ]);
}

$conn->close();
?>