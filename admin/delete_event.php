<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotAdmin();

$id = $_GET['id'] ?? 0;

// Fetch event to get image path
$stmt = $pdo->prepare("SELECT image_path FROM events WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if ($event) {
    // Delete image file if exists
    if ($event['image_path'] && file_exists('../' . $event['image_path'])) {
        unlink('../' . $event['image_path']);
    }
    // Delete event from database
    $delete = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $delete->execute([$id]);
}

header('Location: view_events.php');
exit;