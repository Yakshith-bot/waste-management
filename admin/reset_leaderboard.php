<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotAdmin();

if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
    $pdo->query("UPDATE users SET monthly_score = 0 WHERE role='user'");
    $success = "Monthly leaderboard has been reset.";
}
?>
<?php include '../includes/header.php'; ?>
<h2>Reset Monthly Leaderboard</h2>
<?php if (isset($success)): ?>
    <div class="success"><?= $success ?></div>
    <a href="dashboard.php" class="btn">Back to Dashboard</a>
<?php else: ?>
    <p class="warning">Are you sure? This will set all users' monthly scores to 0.</p>
    <form method="POST">
        <input type="hidden" name="confirm" value="yes">
        <button type="submit" class="btn btn-danger">Yes, Reset</button>
        <a href="dashboard.php" class="btn">Cancel</a>
    </form>
<?php endif; ?>
<?php include '../includes/footer.php'; ?>