<?php
require_once 'config.php';

$category = isset($_GET['category']) ? $_GET['category'] : null;

if ($category) {
    $category = $conn->real_escape_string($category);
    $sql = "SELECT * FROM products WHERE category = '$category' ORDER BY created_at ASC";
} else {
    $sql = "SELECT * FROM products ORDER BY category, created_at ASC";
}

$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

echo json_encode([
    'success' => true,
    'data' => $products
]);

$conn->close();
?>