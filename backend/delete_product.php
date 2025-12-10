<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id = intval($data['id']);

// Items akan otomatis terhapus karena ON DELETE CASCADE
$sql = "DELETE FROM products WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo json_encode([
        'success' => true,
        'message' => 'Product deleted successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete product: ' . $conn->error
    ]);
}

$conn->close();
?>