<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
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
<div class="particle" style="left: 96%; animation-duration: 26s; animation-delay: 3s;">💚</div>
<section style="text-align: center; margin-bottom: 2rem;">
    <h1 style="font-size: 2.5rem; color: var(--primary-dark);">🎮 Games Hub</h1>
    <p style="font-size: 1.2rem;">Choose a game, earn points, and become a waste management hero!</p>
</section>

<div class="game-hub-grid">
    <!-- Waste Segregation Card -->
    <div class="game-card glass-card">
        <div class="game-icon">🗑️🧩</div>
        <h2>Waste Segregation</h2>
        <p>Drag and drop waste items into the correct bins. Each correct drop = 1 point. Max 5 points per play.</p>
        <div style="margin-top: 1.5rem;">
            <a href="games/segregation.php" class="btn">Play Now</a>
        </div>
    </div>
    <!-- Timed Quiz Card -->
    <div class="game-card glass-card">
        <div class="game-icon">⏱️❓</div>
        <h2>Timed Quiz</h2>
        <p>Answer 5 waste‑related questions against the clock. Each correct answer = 1 point.</p>
        <div style="margin-top: 1.5rem;">
            <a href="games/quiz.php" class="btn">Play Now</a>
        </div>
    </div>
</div>

<!-- Optional: Display user's total score -->
<?php if (isLoggedIn()): 
    $stmt = $pdo->prepare("SELECT total_score FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $total = $stmt->fetchColumn();
?>
    <div class="glass-card" style="margin-top: 2rem; text-align: center;">
        <h3>🌟 Your Total Points: <strong style="color: var(--primary-dark);"><?= $total ?></strong></h3>
        <p>Keep playing to climb the leaderboard!</p>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>