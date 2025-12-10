<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$product_id = intval($data['product_id']);
$type = $conn->real_escape_string($data['type']);
$name = $conn->real_escape_string($data['name']);
$img = isset($data['img']) && $data['img'] ? $conn->real_escape_string($data['img']) : NULL;
$bonus = isset($data['bonus']) && $data['bonus'] ? $conn->real_escape_string($data['bonus']) : NULL;
$price = intval($data['price']);
$display_order = isset($data['display_order']) ? intval($data['display_order']) : 0;

$sql = "INSERT INTO items (product_id, type, name, img, bonus, price, display_order) 
        VALUES ($product_id, '$type', '$name', " . 
        ($img ? "'$img'" : "NULL") . ", " .
        ($bonus ? "'$bonus'" : "NULL") . ", $price, $display_order)";

if ($conn->query($sql) === TRUE) {
    echo json_encode([
        'success' => true,
        'message' => 'Item created successfully',
        'item_id' => $conn->insert_id
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to create item: ' . $conn->error
    ]);
}

$conn->close();
?>