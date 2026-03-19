<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotAdmin();

$stmt = $pdo->query("SELECT s.*, u.username FROM scores s JOIN users u ON s.user_id = u.id ORDER BY s.played_at DESC LIMIT 100");
$scores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include '../includes/header.php'; ?>
<h2>Recent Scores</h2>
<table>
    <thead>
        <tr>
            <th>User</th>
            <th>Score</th>
            <th>Game</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($scores as $score): ?>
        <tr>
            <td><?= htmlspecialchars($score['username']) ?></td>
            <td><?= $score['score'] ?></td>
            <td><?= $score['game_type'] ?></td>
            <td><?= $score['played_at'] ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php include '../includes/footer.php'; ?>