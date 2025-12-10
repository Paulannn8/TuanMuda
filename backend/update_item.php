<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$id = intval($data['id']);
$name = $conn->real_escape_string($data['name']);
$img = isset($data['img']) && $data['img'] ? $conn->real_escape_string($data['img']) : NULL;
$bonus = isset($data['bonus']) && $data['bonus'] ? $conn->real_escape_string($data['bonus']) : NULL;
$price = intval($data['price']);
$display_order = isset($data['display_order']) ? intval($data['display_order']) : 0;

$sql = "UPDATE items SET 
        name = '$name',
        img = " . ($img ? "'$img'" : "NULL") . ",
        bonus = " . ($bonus ? "'$bonus'" : "NULL") . ",
        price = $price,
        display_order = $display_order
        WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo json_encode([
        'success' => true,
        'message' => 'Item updated successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update item: ' . $conn->error
    ]);
}

$conn->close();
?>