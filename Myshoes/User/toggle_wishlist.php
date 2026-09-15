<?php
session_start();
require_once 'connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'not_logged_in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$shoe_id = isset($_POST['shoe_id']) ? intval($_POST['shoe_id']) : 0;

if ($shoe_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid shoe ID']);
    exit();
}

// Check if it already exists
$stmt = $connection->prepare("SELECT id FROM wishlist WHERE user_id = ? AND shoe_id = ?");
$stmt->bind_param("ii", $user_id, $shoe_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Exists, so we remove it
    $stmt2 = $connection->prepare("DELETE FROM wishlist WHERE user_id = ? AND shoe_id = ?");
    $stmt2->bind_param("ii", $user_id, $shoe_id);
    if ($stmt2->execute()) {
        echo json_encode(['status' => 'success', 'action' => 'removed']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
} else {
    // Does not exist, add it
    $stmt2 = $connection->prepare("INSERT INTO wishlist (user_id, shoe_id) VALUES (?, ?)");
    $stmt2->bind_param("ii", $user_id, $shoe_id);
    if ($stmt2->execute()) {
        echo json_encode(['status' => 'success', 'action' => 'added']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
}
?>
