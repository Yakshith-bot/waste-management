<?php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        session_regenerate_id(true);
        header('Location: index.php');
        exit;
    } else {
        $error = "Invalid credentials.";
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
<div class="particle" style="left: 96%; animation-duration: 26s; animation-delay: 3s;">💚</div>
<div class="form-container">
    <h2>Login</h2>
    <?php if (isset($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['register_success'])): ?>
        <div class="success"><?= $_SESSION['register_success']; unset($_SESSION['register_success']); ?></div>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username or Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" class="btn">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>
<?php include 'includes/footer.php'; ?>