<?php
require_once 'config.php';

// Hanya terima POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Ambil data dari request
$data = json_decode(file_get_contents('php://input'), true);

// Validasi data
if (!isset($data['order_id']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

$order_id = $conn->real_escape_string($data['order_id']);
$status = $conn->real_escape_string($data['status']);

// Validasi status (hanya boleh pending, success, atau failed)
if (!in_array($status, ['pending', 'success', 'failed'])) {
    echo json_encode(['success' => false, 'message' => 'Status tidak valid']);
    exit;
}

// Query UPDATE
$sql = "UPDATE transactions SET status = '$status' WHERE order_id = '$order_id'";

if ($conn->query($sql) === TRUE) {
    if ($conn->affected_rows > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Status berhasil diupdate',
            'order_id' => $order_id,
            'new_status' => $status
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Order ID tidak ditemukan'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Gagal update status: ' . $conn->error
    ]);
}

$conn->close();
?>