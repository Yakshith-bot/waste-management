<?php
// No need to start session here – config.php does it
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WasteAware ♻️</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
   
</head>
<body class="bg-gradient-to-br from-green-50 to-green-100 relative overflow-x-hidden">
    <header>
        <div class="logo">♻️ WasteAware</div>
        <nav>
            <ul>
                <li><a href="<?= BASE_URL ?>index.php">Home</a></li>
                <li><a href="<?= BASE_URL ?>learn.php">Learn</a></li>
                <li><a href="<?= BASE_URL ?>games.php">Games</a></li>
                <li><a href="<?= BASE_URL ?>events.php">Events</a></li>
                <li><a href="<?= BASE_URL ?>leaderboard.php">Leaderboard</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="<?= BASE_URL ?>logout.php">Logout (<?= htmlspecialchars($_SESSION['username']) ?>)</a></li>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <li><a href="<?= BASE_URL ?>admin/dashboard.php">Admin</a></li>
                    <?php endif; ?>
                <?php else: ?>
                    <li><a href="<?= BASE_URL ?>login.php">Login</a></li>
                    <li><a href="<?= BASE_URL ?>register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>