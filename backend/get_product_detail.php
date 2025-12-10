<?php
require_once 'config.php';

if (!isset($_GET['id']) && !isset($_GET['name'])) {
    echo json_encode(['success' => false, 'message' => 'Product ID or Name required']);
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM products WHERE id = $id";
} else {
    $name = $conn->real_escape_string($_GET['name']);
    $sql = "SELECT * FROM products WHERE name = '$name'";
}

$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

$product = $result->fetch_assoc();

// Get items
$product_id = $product['id'];
$items_sql = "SELECT * FROM items WHERE product_id = $product_id ORDER BY type, display_order";
$items_result = $conn->query($items_sql);

$diamonds = [];
$packages = [];

while($item = $items_result->fetch_assoc()) {
    if ($item['type'] === 'diamond') {
        $diamonds[] = $item;
    } else {
        $packages[] = $item;
    }
}

$product['diamonds'] = $diamonds;
$product['packages'] = $packages;

echo json_encode([
    'success' => true,
    'data' => $product
]);

$conn->close();
?>