<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotLoggedIn();

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT daily_score, last_played_date FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$today = date('Y-m-d');
$dailyLimitReached = ($user['last_played_date'] === $today && $user['daily_score'] >= 10000);
?>
<?php include '../includes/header.php'; ?>

<!-- Hidden confetti canvas (optional) -->
<canvas id="confetti-canvas" style="display: none;"></canvas>

<div class="quiz-wrapper">
    <h1 style="text-align: center; color: var(--primary-dark); margin-bottom: 0.5rem;">
        ⏱️ Timed Waste Quiz
    </h1>
    <p style="text-align: center; font-size: 1.2rem; margin-bottom: 2rem;">
        Answer 5 questions before time runs out!
    </p>

    <?php if ($dailyLimitReached): ?>
        <div class="daily-limit-card">
            <h3>⚠️ Daily Limit Reached</h3>
            <p style="font-size: 1.2rem;">You've earned 10000 points today. Come back tomorrow for more!</p>
            <a href="../games.php" class="btn" style="margin-top: 1rem;">🎮 Back to Games</a>
        </div>
    <?php else: ?>
        <!-- Quiz Container – will be populated by JavaScript -->
        <div id="quiz-root"></div>
    <?php endif; ?>
</div>

<script>
    // Pass PHP variables to JS
    window.userId = <?= $userId ?>;
    window.dailyLimitReached = <?= $dailyLimitReached ? 'true' : 'false' ?>;
    window.BASE_URL = '<?= BASE_URL ?>';
</script>
<script src="<?= BASE_URL ?>assets/js/quiz.js"></script>

<?php include '../includes/footer.php'; ?>