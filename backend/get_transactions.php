<?php
require_once 'config.php';

// Query untuk ambil semua transaksi
$sql = "SELECT * FROM transactions ORDER BY created_at DESC";
$result = $conn->query($sql);

$transactions = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'count' => count($transactions),
        'data' => $transactions
    ]);
} else {
    echo json_encode([
        'success' => true,
        'count' => 0,
        'data' => [],
        'message' => 'Belum ada transaksi'
    ]);
}

$conn->close();
?>