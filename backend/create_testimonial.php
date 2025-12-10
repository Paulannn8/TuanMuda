<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$name = $conn->real_escape_string($data['name']);
$gender = $conn->real_escape_string($data['gender']);
$rating = floatval($data['rating']);
$text = $conn->real_escape_string($data['text']);
$display_order = isset($data['display_order']) ? intval($data['display_order']) : 0;

// Generate unique avatar seed yang belum dipakai
function getUniqueAvatarSeed($conn, $gender) {
    $maxAttempts = 100;
    
    for ($i = 0; $i < $maxAttempts; $i++) {
        $seed = rand(1, 99);
        
        // Cek apakah seed sudah dipakai dengan gender yang sama
        $checkSql = "SELECT COUNT(*) as count FROM testimonials WHERE avatar_seed = $seed AND gender = '$gender'";
        $result = $conn->query($checkSql);
        $row = $result->fetch_assoc();
        
        if ($row['count'] == 0) {
            return $seed;
        }
    }
    
    // Fallback jika sudah banyak testimonial
    return rand(1, 99);
}

$avatar_seed = getUniqueAvatarSeed($conn, $gender);

$sql = "INSERT INTO testimonials (name, gender, rating, text, avatar_seed, display_order) 
        VALUES ('$name', '$gender', $rating, '$text', $avatar_seed, $display_order)";

if ($conn->query($sql) === TRUE) {
    echo json_encode([
        'success' => true,
        'message' => 'Testimonial berhasil ditambahkan',
        'testimonial_id' => $conn->insert_id,
        'avatar_seed' => $avatar_seed
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Gagal menambahkan testimonial: ' . $conn->error
    ]);
}

$conn->close();
?>