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
$gender = $conn->real_escape_string($data['gender']);
$rating = floatval($data['rating']);
$text = $conn->real_escape_string($data['text']);
$display_order = intval($data['display_order']);

// Jika gender berubah, generate avatar seed baru
$checkGenderSql = "SELECT gender, avatar_seed FROM testimonials WHERE id = $id";
$result = $conn->query($checkGenderSql);
$current = $result->fetch_assoc();

$avatar_seed = $current['avatar_seed'];

if ($current['gender'] !== $gender) {
    // Generate avatar seed baru untuk gender baru
    function getUniqueAvatarSeed($conn, $gender, $excludeId) {
        $maxAttempts = 100;
        
        for ($i = 0; $i < $maxAttempts; $i++) {
            $seed = rand(1, 99);
            
            $checkSql = "SELECT COUNT(*) as count FROM testimonials 
                        WHERE avatar_seed = $seed AND gender = '$gender' AND id != $excludeId";
            $result = $conn->query($checkSql);
            $row = $result->fetch_assoc();
            
            if ($row['count'] == 0) {
                return $seed;
            }
        }
        
        return rand(1, 99);
    }
    
    $avatar_seed = getUniqueAvatarSeed($conn, $gender, $id);
}

$sql = "UPDATE testimonials SET 
        name = '$name',
        gender = '$gender',
        rating = $rating,
        text = '$text',
        avatar_seed = $avatar_seed,
        display_order = $display_order
        WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo json_encode([
        'success' => true,
        'message' => 'Testimonial berhasil diupdate',
        'avatar_seed' => $avatar_seed
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Gagal mengupdate testimonial: ' . $conn->error
    ]);
}

$conn->close();
?>