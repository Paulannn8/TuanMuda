<?php
require_once 'config.php';

// Cek apakah request dari admin
$showAll = isset($_GET['show_all']) && $_GET['show_all'] === 'true';

if ($showAll) {
    // Untuk admin: tampilkan semua testimonial
    $sql = "SELECT * FROM testimonials ORDER BY display_order ASC";
} else {
    // Untuk client: hanya testimonial aktif
    $sql = "SELECT * FROM testimonials WHERE is_active = 1 ORDER BY display_order ASC";
}

$result = $conn->query($sql);

$testimonials = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $testimonials[] = $row;
    }
}

echo json_encode([
    'success' => true,
    'data' => $testimonials,
    'count' => count($testimonials)
]);

$conn->close();
?>