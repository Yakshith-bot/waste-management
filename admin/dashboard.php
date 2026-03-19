<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotAdmin();

// Counts
$usersCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();
$eventsCount = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
$totalPlays = $pdo->query("SELECT COUNT(*) FROM scores")->fetchColumn();
?>
<?php include '../includes/header.php'; ?>
<h1>Admin Dashboard</h1>
<div class="admin-cards">
    <div class="card">
        <h3>Total Users</h3>
        <p><?= $usersCount ?></p>
    </div>
    <div class="card">
        <h3>Events Posted</h3>
        <p><?= $eventsCount ?></p>
    </div>
    <div class="card">
        <h3>Total Plays</h3>
        <p><?= $totalPlays ?></p>
    </div>
</div>
<div class="admin-actions">
    <a href="view_users.php" class="btn">View Users</a>
    <!-- <a href="view_scores.php" class="btn">View Scores</a> -->
    <a href="view_events.php" class="btn">📋 Manage Events</a> <!-- NEW -->
    <a href="post_event.php" class="btn">📅 Post Event</a>
    <a href="highlight_event.php" class="btn">⭐ Highlight Event</a>
    <a href="reset_leaderboard.php" class="btn btn-warning">Reset Monthly Leaderboard</a>
</div>
<?php include '../includes/footer.php'; ?>