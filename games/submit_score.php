<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
    exit;
}

$userId = $_SESSION['user_id'];
$score = intval($_POST['score'] ?? 0);
$game = $_POST['game'] ?? '';

if ($score <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid score']);
    exit;
}

// Get current user data
$stmt = $pdo->prepare("SELECT daily_score, last_played_date, monthly_score, total_score FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$today = date('Y-m-d');

// Reset daily score if new day
if ($user['last_played_date'] !== $today) {
    $dailyScore = 0;
    $lastPlayed = $today;
} else {
    $dailyScore = $user['daily_score'];
    $lastPlayed = $user['last_played_date'];
}

// Check daily limit
if ($dailyScore + $score > 10000) {
    echo json_encode(['success' => false, 'message' => 'Daily limit reached (max 10000 points)']);
    exit;
}

// Update totals
$newDaily = $dailyScore + $score;
$newMonthly = $user['monthly_score'] + $score;
$newTotal = $user['total_score'] + $score;

$update = $pdo->prepare("UPDATE users SET daily_score = ?, monthly_score = ?, total_score = ?, last_played_date = ? WHERE id = ?");
$update->execute([$newDaily, $newMonthly, $newTotal, $today, $userId]);

// Insert score record
$insert = $pdo->prepare("INSERT INTO scores (user_id, score, game_type) VALUES (?, ?, ?)");
$insert->execute([$userId, $score, $game]);

echo json_encode(['success' => true, 'daily' => $newDaily, 'limit' => ($newDaily >= 10000)]);
exit;