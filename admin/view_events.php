<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotAdmin();

// Fetch all events, newest first
$stmt = $pdo->query("SELECT * FROM events ORDER BY created_at DESC");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include '../includes/header.php'; ?>

<h1>📋 All Events</h1>

<div style="display: flex; justify-content: flex-end; margin-bottom: 1rem;">
    <a href="post_event.php" class="btn">➕ New Event</a>
</div>

<?php if (count($events) === 0): ?>
    <p class="glass-card">No events posted yet.</p>
<?php else: ?>
    <div class="event-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
        <?php foreach ($events as $event): ?>
            <div class="glass-card" style="padding: 1.5rem; display: flex; flex-direction: column;">
                <?php if ($event['image_path']): ?>
                    <img src="<?= BASE_URL . $event['image_path'] ?>" alt="<?= htmlspecialchars($event['title']) ?>" style="width: 100%; height: 180px; object-fit: cover; border-radius: 12px; margin-bottom: 1rem;">
                <?php endif; ?>
                <h3><?= htmlspecialchars($event['title']) ?></h3>
                <p style="margin: 0.5rem 0;"><?= nl2br(htmlspecialchars($event['description'])) ?></p>
                <?php if ($event['event_date']): ?>
                    <p><strong>📅 Date:</strong> <?= date('F j, Y', strtotime($event['event_date'])) ?></p>
                <?php endif; ?>
                <p><small>Posted: <?= date('M j, Y', strtotime($event['created_at'])) ?></small></p>
                <div style="margin-top: 1rem; display: flex; gap: 0.8rem; flex-wrap: wrap;">
                    <a href="highlight_event.php?event_id=<?= $event['id'] ?>" class="btn-small <?= $event['is_highlight'] ? 'btn-warning' : 'btn-outline' ?>">
                        <?= $event['is_highlight'] ? '⭐ Highlighted' : 'Make Highlight' ?>
                    </a>
                    <a href="edit_event.php?id=<?= $event['id'] ?>" class="btn-small">✏️ Edit</a>
                    <a href="delete_event.php?id=<?= $event['id'] ?>" class="btn-small btn-danger" onclick="return confirm('Delete this event?')">🗑️ Delete</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>