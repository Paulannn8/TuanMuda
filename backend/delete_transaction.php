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

// Query DELETE
$sql = "DELETE FROM transactions WHERE order_id = '$order_id'";

if ($conn->query($sql) === TRUE) {
    if ($conn->affected_rows > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Transaksi berhasil dihapus',
            'order_id' => $order_id
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
        'message' => 'Gagal menghapus transaksi: ' . $conn->error
    ]);
}

$conn->close();
?>