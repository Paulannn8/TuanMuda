<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id = intval($data['id']);
$is_active = intval($data['is_active']);

// Log untuk debugging
error_log("Toggle testimonial ID: $id to is_active: $is_active");

$sql = "UPDATE testimonials SET is_active = $is_active WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    if ($conn->affected_rows > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Status testimonial berhasil diubah',
            'id' => $id,
            'is_active' => $is_active
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Tidak ada perubahan atau ID tidak ditemukan'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Gagal mengubah status: ' . $conn->error
    ]);
}

$conn->close();
?>