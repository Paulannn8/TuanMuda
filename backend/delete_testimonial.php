<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id = intval($data['id']);

$sql = "DELETE FROM testimonials WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo json_encode([
        'success' => true,
        'message' => 'Testimonial berhasil dihapus'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Gagal menghapus testimonial: ' . $conn->error
    ]);
}

$conn->close();
?>