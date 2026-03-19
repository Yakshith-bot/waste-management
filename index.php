<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Get highlighted event
$stmt = $pdo->query("SELECT * FROM events WHERE is_highlight = 1 ORDER BY created_at DESC LIMIT 1");
$highlight = $stmt->fetch(PDO::FETCH_ASSOC);

// Get top 5 users by monthly_score
$stmt = $pdo->query("SELECT username, monthly_score FROM users WHERE role='user' ORDER BY monthly_score DESC LIMIT 5");
$topUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get current user's daily progress (if logged in)
$dailyScore = 0;
$dailyLimitReached = false;
if (isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT daily_score, last_played_date FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    $today = date('Y-m-d');
    if ($userData['last_played_date'] === $today) {
        $dailyScore = $userData['daily_score'];
        $dailyLimitReached = $dailyScore >= 10000;
    }
}
?>
<?php include 'includes/header.php'; ?>

<!-- Font Awesome Icons (if not already in header) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<!-- Animated Background Particles – BOLD & LOOPING -->
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
<!-- ===== HERO SECTION ===== -->
<section class="hero-section">
    <div class="hero-content">
        <span class="hero-badge">♻️ WasteAware</span>
        <h1 class="hero-title">Play, Learn,<br>Save the Planet</h1>
        <p class="hero-subtitle">Turn waste management into a fun challenge and earn points while making a real impact.</p>
        <div class="hero-actions">
            <a href="<?= BASE_URL ?>games.php" class="btn btn-large">🎮 Explore Games</a>
            <a href="<?= BASE_URL ?>learn.php" class="btn btn-outline btn-large">📚 Learn More</a>
        </div>
    </div>
    <div class="hero-image">
        <img src="<?= BASE_URL ?>assets/images/hero-illustration.svg" alt="Waste segregation illustration" onerror="this.style.display='none'">
    </div>
</section>

<!-- ===== COMPACT HIGHLIGHTED EVENT ===== -->
<section class="highlight-event-compact">
    <?php if ($highlight): ?>
        <div class="event-compact-card glass-card">
            <?php if ($highlight['image_path']): ?>
                <div class="event-compact-image">
                    <img src="<?= BASE_URL . $highlight['image_path'] ?>" 
                         alt="<?= htmlspecialchars($highlight['title']) ?>">
                </div>
            <?php endif; ?>
            <div class="event-compact-details">
                <span class="event-badge">🌟 Featured</span>
                <h3><?= htmlspecialchars($highlight['title']) ?></h3>
                <p class="event-description"><?= nl2br(htmlspecialchars($highlight['description'])) ?></p>
                <?php if ($highlight['event_date']): ?>
                    <p class="event-date">
                        <i class="fas fa-calendar-alt"></i> <?= date('M j, Y', strtotime($highlight['event_date'])) ?>
                    </p>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>events.php" class="btn-small">View All Events →</a>
            </div>
        </div>
    <?php else: ?>
        <div class="glass-card" style="text-align: center; padding: 1.5rem;">
            <span style="font-size: 2rem;">📅</span>
            <h3 style="margin: 0.5rem 0;">No Featured Event</h3>
            <p style="color: #666; margin-bottom: 1rem;">Check back soon for upcoming events!</p>
            <a href="<?= BASE_URL ?>events.php" class="btn-small">Browse Events</a>
        </div>
    <?php endif; ?>
</section>

<!-- ===== WHY IT MATTERS (IMPACT GRID) ===== -->
<section class="impact-section">
    <div class="section-header">
        <span class="section-tag">🌍 Our Mission</span>
        <h2>Why Waste Management Matters</h2>
    </div>
    <div class="impact-grid">
        <div class="impact-card glass-card">
            <div class="impact-icon">🗑️</div>
            <h3>2.12B tons/year</h3>
            <p>Global waste generation – enough to fill 800,000 Olympic pools.</p>
        </div>
        <div class="impact-card glass-card">
            <div class="impact-icon">♻️</div>
            <h3>80% recyclable</h3>
            <p>Of what we throw away could be recycled or composted.</p>
        </div>
        <div class="impact-card glass-card">
            <div class="impact-icon">⏳</div>
            <h3>450 years</h3>
            <p>Time for a plastic bottle to decompose in a landfill.</p>
        </div>
        <div class="impact-card glass-card">
            <div class="impact-icon">💚</div>
            <h3>60% reduction</h3>
            <p>Landfill waste could be cut by 60% with proper segregation.</p>
        </div>
    </div>
</section>

<!-- ===== GAMES PREVIEW ===== -->
<section class="games-preview">
    <div class="section-header">
        <span class="section-tag">🎮 Play & Earn</span>
        <h2>Featured Games</h2>
        <p class="section-subtitle">Test your knowledge and earn up to 10,000 points per day!</p>
    </div>
    <div class="game-hub-grid">
        <div class="game-card glass-card">
            <div class="game-icon">🗑️🧩</div>
            <h3>Waste Segregation</h3>
            <p>Drag and drop waste items into the correct bins. 100+ unique items – every game is different!</p>
            <div class="game-features">
                <span><i class="fas fa-star"></i> 1 pt per item</span>
                <span><i class="fas fa-rotate"></i> 5 items per round</span>
            </div>
            <a href="<?= BASE_URL ?>games/segregation.php" class="btn btn-large">Play Now</a>
        </div>
        <div class="game-card glass-card">
            <div class="game-icon">⏱️❓</div>
            <h3>Timed Quiz</h3>
            <p>Answer 5 waste‑related questions against the clock. Learn while you race!</p>
            <div class="game-features">
                <span><i class="fas fa-clock"></i> 20 seconds</span>
                <span><i class="fas fa-check-circle"></i> 1 pt per correct</span>
            </div>
            <a href="<?= BASE_URL ?>games/quiz.php" class="btn btn-large">Play Now</a>
        </div>
    </div>
</section>

<!-- ===== LEADERBOARD & PROGRESS ===== -->
<section class="stats-dashboard">
    <div class="section-header">
        <span class="section-tag">🏆 Rankings</span>
        <h2>Monthly Leaderboard</h2>
    </div>
    <div class="stats-grid">
        <!-- Leaderboard Card -->
        <div class="glass-card leaderboard-card">
            <div class="leaderboard-header">
                <h3>🏆 Top 5 Players</h3>
                <span class="month-badge"><?= date('F Y') ?></span>
            </div>
            <ol class="leaderboard-list">
                <?php foreach ($topUsers as $index => $user): ?>
                <li class="leaderboard-item <?= $index === 0 ? 'first' : '' ?>">
                    <span class="rank"><?= $index+1 ?></span>
                    <span class="user-name"><?= htmlspecialchars($user['username']) ?></span>
                    <span class="user-score"><?= number_format($user['monthly_score']) ?> pts</span>
                </li>
                <?php endforeach; ?>
            </ol>
            <a href="<?= BASE_URL ?>leaderboard.php" class="btn-link">View Full Leaderboard →</a>
        </div>

        <!-- Daily Progress Card -->
        <?php if (isLoggedIn()): ?>
        <div class="glass-card progress-card">
            <div class="progress-header">
                <h3>📅 Your Daily Progress</h3>
                <span class="daily-limit-badge">Max 10,000 pts</span>
            </div>
            <div class="daily-score-display">
                <span class="current-score"><?= number_format($dailyScore) ?></span>
                <span class="separator">/</span>
                <span class="max-score">10,000</span>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar" style="width: <?= min($dailyScore, 10000) / 10000 * 100 ?>%;"></div>
                <div class="progress-text"><?= $dailyScore ?>/10000 pts</div>
            </div>
            <?php if ($dailyLimitReached): ?>
                <div class="limit-reached-message">
                    🎉 Daily limit reached! Come back tomorrow.
                </div>
            <?php else: ?>
                <p class="progress-cta">Keep playing to reach 10,000 points!</p>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="glass-card login-prompt">
            <div class="prompt-icon">🔐</div>
            <h3>Track Your Progress</h3>
            <p>Login or register to earn points and climb the leaderboard!</p>
            <div class="prompt-actions">
                <a href="<?= BASE_URL ?>login.php" class="btn">Login</a>
                <a href="<?= BASE_URL ?>register.php" class="btn btn-outline">Register</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>