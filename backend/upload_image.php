<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Cek apakah ada file yang diupload
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
    exit;
}

// Ambil tipe (product atau item)
$type = isset($_POST['type']) ? $_POST['type'] : 'products';

// Validasi tipe
if (!in_array($type, ['products', 'items'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid type']);
    exit;
}

$file = $_FILES['image'];
$fileName = $file['name'];
$fileTmpName = $file['tmp_name'];
$fileSize = $file['size'];
$fileError = $file['error'];

// Ambil ekstensi file
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

// Ekstensi yang diperbolehkan
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

// Validasi ekstensi
if (!in_array($fileExt, $allowedExtensions)) {
    echo json_encode(['success' => false, 'message' => 'File type not allowed. Use: jpg, jpeg, png, gif, webp']);
    exit;
}

// Validasi ukuran file (max 5MB)
if ($fileSize > 5242880) {
    echo json_encode(['success' => false, 'message' => 'File too large. Max 5MB']);
    exit;
}

// Generate nama file unik
$newFileName = uniqid('', true) . '.' . $fileExt;

// Path upload
$uploadPath = "../uploads/$type/" . $newFileName;

// Upload file
if (move_uploaded_file($fileTmpName, $uploadPath)) {
    // Return path relative untuk disimpan di database
    $relativePath = "uploads/$type/" . $newFileName;
    
    echo json_encode([
        'success' => true,
        'message' => 'File uploaded successfully',
        'path' => $relativePath,
        'filename' => $newFileName
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
}
?>