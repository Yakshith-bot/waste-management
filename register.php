<?php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    $errors = [];

    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        try {
            $stmt->execute([$username, $email, $hashed]);
            $_SESSION['register_success'] = "Registration successful. Please login.";
            header('Location: login.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Username or email already exists.";
        }
    }
}
?>
<?php include 'includes/header.php'; ?>
<div class="form-container">
    <h2>Register</h2>
    <?php if (!empty($errors)): ?>
        <div class="error"><?= implode('<br>', $errors) ?></div>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit" class="btn">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>
<?php include 'includes/footer.php'; ?>