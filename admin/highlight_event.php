<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotAdmin();

$events = $pdo->query("SELECT * FROM events ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    // Reset all highlights
    $pdo->query("UPDATE events SET is_highlight = 0");
    // Set selected as highlight
    $stmt = $pdo->prepare("UPDATE events SET is_highlight = 1 WHERE id = ?");
    $stmt->execute([$_POST['event_id']]);
    $success = "Highlight updated.";
    $events = $pdo->query("SELECT * FROM events ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
}
?>
<?php include '../includes/header.php'; ?>

<h1>⭐ Highlight an Event</h1>

<?php if (isset($success)): ?>
    <div class="success"><?= $success ?></div>
<?php endif; ?>

<form method="POST" class="form-container">
    <div class="form-group">
        <label for="event_id">Select event to highlight:</label>
        <select name="event_id" id="event_id" required>
            <option value="">-- Choose an event --</option>
            <?php foreach ($events as $event): ?>
                <option value="<?= $event['id'] ?>" <?= $event['is_highlight'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($event['title']) ?> 
                    (<?= date('M j, Y', strtotime($event['created_at'])) ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit" class="btn">Set as Highlight</button>
    <a href="dashboard.php" class="btn btn-outline">Back</a>
</form>

<?php include '../includes/footer.php'; ?>