<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Monthly top 10
$monthlyStmt = $pdo->query("SELECT username, monthly_score FROM users WHERE role='user' ORDER BY monthly_score DESC LIMIT 10");
$monthlyTop = $monthlyStmt->fetchAll(PDO::FETCH_ASSOC);

// All-time top 10
$totalStmt = $pdo->query("SELECT username, total_score FROM users WHERE role='user' ORDER BY total_score DESC LIMIT 10");
$totalTop = $totalStmt->fetchAll(PDO::FETCH_ASSOC);

// Current user's rank (monthly)
$userRank = null;
if (isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT id, monthly_score FROM users WHERE role='user' ORDER BY monthly_score DESC");
    $stmt->execute();
    $rank = 1;
    foreach ($stmt->fetchAll() as $row) {
        if ($row['id'] == $_SESSION['user_id']) {
            $userRank = $rank;
            break;
        }
        $rank++;
    }
}
?>
<?php include 'includes/header.php'; ?>
<div class="particle" style="left: 2%; animation-duration: 22s; animation-delay: 0s;">♻️</div>
<div class="particle" style="left: 8%; animation-duration: 28s; animation-delay: 3s;">🌿</div>
<div class="particle" style="left: 15%; animation-duration: 25s; animation-delay: 1s;">🗑️</div>
<div class="particle" style="left: 22%; animation-duration: 32s; animation-delay: 7s;">♻️</div>
<div class="particle" style="left: 30%; animation-duration: 24s; animation-delay: 2s;">🌍</div>
<div class="particle" style="left: 38%; animation-duration: 29s; animation-delay: 5s;">♻️</div>
<div class="particle" style="left: 45%; animation-duration: 26s; animation-delay: 8s;">🍃</div>
<div class="particle" style="left: 52%; animation-duration: 31s; animation-delay: 4s;">💚</div>
<div class="particle" style="left: 60%; animation-duration: 23s; animation-delay: 9s;">🌱</div>
<div class="particle" style="left: 68%; animation-duration: 27s; animation-delay: 2s;">♻️</div>
<div class="particle" style="left: 75%; animation-duration: 33s; animation-delay: 6s;">🗑️</div>
<div class="particle" style="left: 82%; animation-duration: 24s; animation-delay: 1s;">🌿</div>
<div class="particle" style="left: 90%; animation-duration: 30s; animation-delay: 5s;">♻️</div>
<div class="particle" style="left: 96%; animation-duration: 26s; animation-delay: 3s;">💚</div></tr></tr>

<h1 style="text-align: center; color: var(--primary-dark); margin-bottom: 2rem;">🏆 Leaderboard</h1>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
    <!-- MONTHLY TOP 10 -->
    <div class="glass-card">
        <h2 style="margin-bottom: 1.2rem;">📅 This Month</h2>
        <table class="leaderboard-table">
            <thead>
                <tr><th>Rank</th><th>User</th><th>Points</th></tr>
            </thead>
            <tbody>
                <?php foreach ($monthlyTop as $index => $user): ?>
                <tr>
                    <td><strong>#<?= $index+1 ?></strong></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= $user['monthly_score'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <!-- ALL-TIME TOP 10 -->
    <div class="glass-card">
        <h2 style="margin-bottom: 1.2rem;">🏅 All Time</h2>
        <table class="leaderboard-table">
            <thead>
                <tr><th>Rank</th><th>User</th><th>Total Points</th></tr>
            </thead>
            <tbody>
                <?php foreach ($totalTop as $index => $user): ?>
                <tr>
                    <td><strong>#<?= $index+1 ?></strong></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= $user['total_score'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- CURRENT USER RANK -->
<?php if (isLoggedIn() && $userRank): ?>
<div class="glass-card" style="margin-top: 2rem; text-align: center;">
    <h3>✨ Your Monthly Rank: <span style="color: var(--primary-dark);">#<?= $userRank ?></span></h3>
    <p>Keep playing to improve your position!</p>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>