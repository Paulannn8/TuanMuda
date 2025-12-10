<?php
session_start();
require_once 'config.php';

// Cek login admin
if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$name = $conn->real_escape_string($data['name']);
$category = $conn->real_escape_string($data['category']);
$img = $conn->real_escape_string($data['img']);
$tag = $conn->real_escape_string($data['tag']);
$title = $conn->real_escape_string($data['title']);
$cara = $conn->real_escape_string($data['cara']);
$item_label = $conn->real_escape_string($data['item_label']);
$package_label = isset($data['package_label']) ? $conn->real_escape_string($data['package_label']) : NULL;
$input_type = $conn->real_escape_string($data['input_type']);
$input_label = $conn->real_escape_string($data['input_label']);
$input_placeholder_userid = isset($data['input_placeholder_userid']) ? $conn->real_escape_string($data['input_placeholder_userid']) : NULL;
$input_placeholder_zoneid = isset($data['input_placeholder_zoneid']) ? $conn->real_escape_string($data['input_placeholder_zoneid']) : NULL;
$input_help = isset($data['input_help']) ? $conn->real_escape_string($data['input_help']) : NULL;

$sql = "INSERT INTO products (name, category, img, tag, title, cara, item_label, package_label, input_type, input_label, input_placeholder_userid, input_placeholder_zoneid, input_help) 
        VALUES ('$name', '$category', '$img', '$tag', '$title', '$cara', '$item_label', " . 
        ($package_label ? "'$package_label'" : "NULL") . ", '$input_type', '$input_label', " .
        ($input_placeholder_userid ? "'$input_placeholder_userid'" : "NULL") . ", " .
        ($input_placeholder_zoneid ? "'$input_placeholder_zoneid'" : "NULL") . ", " .
        ($input_help ? "'$input_help'" : "NULL") . ")";

if ($conn->query($sql) === TRUE) {
    echo json_encode([
        'success' => true,
        'message' => 'Product created successfully',
        'product_id' => $conn->insert_id
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to create product: ' . $conn->error
    ]);
}

$conn->close();
?>