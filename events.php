<?php
require_once 'includes/config.php';

// Fetch all events, newest first
$stmt = $pdo->query("SELECT * FROM events ORDER BY created_at DESC");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'includes/header.php'; ?>

<h1 style="text-align: center; color: var(--primary-dark); margin-bottom: 0.5rem;">📅 Upcoming & Past Events</h1>
<p style="text-align: center; font-size: 1.2rem; margin-bottom: 2rem;">Stay updated with our waste awareness activities!</p>

<?php if (count($events) === 0): ?>
    <div class="glass-card" style="text-align: center;">
        <p>No events posted yet. Please check back later.</p>
    </div>
<?php else: ?>
    <div class="event-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 2rem;">
        <?php foreach ($events as $event): ?>
            <div class="glass-card" style="padding: 1.8rem; display: flex; flex-direction: column; height: 100%;">
                <?php if ($event['image_path']): ?>
                    <img src="<?= BASE_URL . $event['image_path'] ?>" 
                         alt="<?= htmlspecialchars($event['title']) ?>" 
                         style="width: 100%; height: 200px; object-fit: cover; border-radius: 12px; margin-bottom: 1rem;">
                <?php endif; ?>
                <h3 style="margin-bottom: 0.5rem;"><?= htmlspecialchars($event['title']) ?></h3>
                <p style="margin-bottom: 1rem;"><?= nl2br(htmlspecialchars($event['description'])) ?></p>
                <?php if ($event['event_date']): ?>
                    <p style="margin-top: auto; font-weight: 600;">
                        📅 <?= date('F j, Y', strtotime($event['event_date'])) ?>
                    </p>
                <?php endif; ?>
                <p style="font-size: 0.9rem; color: #666;">
                    Posted: <?= date('M j, Y', strtotime($event['created_at'])) ?>
                </p>
                <?php if ($event['is_highlight']): ?>
                    <span style="display: inline-block; margin-top: 0.5rem; background: var(--accent); color: white; padding: 0.2rem 0.8rem; border-radius: 50px; font-size: 0.8rem; align-self: flex-start;">
                        ⭐ Highlighted
                    </span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>