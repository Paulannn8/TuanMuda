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
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
    exit;
}

// Generate Order ID unik
$order_id = 'TM' . date('Ymd') . sprintf('%04d', rand(1, 9999));

// Ambil data dari request
$product_name = $conn->real_escape_string($data['product_name']);
$item_name = $conn->real_escape_string($data['item_name']);
$item_price = intval($data['item_price']);
$payment_method = $conn->real_escape_string($data['payment_method']);
$payment_category = $conn->real_escape_string($data['payment_category']);
$total_price = intval($data['total_price']);

// Data user (sesuai tipe input)
$user_id = isset($data['user_input']['userId']) ? $conn->real_escape_string($data['user_input']['userId']) : NULL;
$zone_id = isset($data['user_input']['zoneId']) ? $conn->real_escape_string($data['user_input']['zoneId']) : NULL;
$email = isset($data['user_input']['email']) ? $conn->real_escape_string($data['user_input']['email']) : NULL;
$phone = isset($data['user_input']['phone']) ? $conn->real_escape_string($data['user_input']['phone']) : NULL;

// Query INSERT
$sql = "INSERT INTO transactions (
    order_id, product_name, item_name, item_price, 
    payment_method, payment_category, total_price, 
    user_id, zone_id, email, phone, status
) VALUES (
    '$order_id', '$product_name', '$item_name', $item_price,
    '$payment_method', '$payment_category', $total_price,
    " . ($user_id ? "'$user_id'" : "NULL") . ",
    " . ($zone_id ? "'$zone_id'" : "NULL") . ",
    " . ($email ? "'$email'" : "NULL") . ",
    " . ($phone ? "'$phone'" : "NULL") . ",
    'pending'
)";

if ($conn->query($sql) === TRUE) {
    echo json_encode([
        'success' => true,
        'message' => 'Transaksi berhasil disimpan',
        'order_id' => $order_id,
        'data' => [
            'product_name' => $product_name,
            'item_name' => $item_name,
            'total_price' => $total_price,
            'payment_method' => $payment_method
        ]
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Gagal menyimpan transaksi: ' . $conn->error
    ]);
}

$conn->close();
?>