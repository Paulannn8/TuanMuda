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
if (!isset($data['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Order ID tidak ditemukan']);
    exit;
}

$order_id = $conn->real_escape_string($data['order_id']);

// Cek apakah transaksi masih pending (hanya pending yang bisa dibatalkan)
$checkSql = "SELECT status FROM transactions WHERE order_id = '$order_id'";
$result = $conn->query($checkSql);

if ($result->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Transaksi tidak ditemukan'
    ]);
    exit;
}

$transaction = $result->fetch_assoc();

if ($transaction['status'] !== 'pending') {
    echo json_encode([
        'success' => false,
        'message' => 'Hanya transaksi dengan status PENDING yang dapat dibatalkan'
    ]);
    exit;
}

// Update status menjadi failed (dibatalkan)
$updateSql = "UPDATE transactions SET status = 'failed' WHERE order_id = '$order_id'";

if ($conn->query($updateSql) === TRUE) {
    echo json_encode([
        'success' => true,
        'message' => 'Transaksi berhasil dibatalkan',
        'order_id' => $order_id
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Gagal membatalkan transaksi: ' . $conn->error
    ]);
}

$conn->close();
?>