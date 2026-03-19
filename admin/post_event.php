<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotAdmin();

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $event_date = !empty($_POST['event_date']) ? $_POST['event_date'] : null;

    // Validation
    if (empty($title) || empty($description)) {
        $error = 'Title and description are required.';
    } else {
        // Handle image upload
        $image_path = null;
        if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../assets/uploads/events/';
            // Create directory if not exists
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_tmp = $_FILES['event_image']['tmp_name'];
            $file_name = $_FILES['event_image']['name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array($file_ext, $allowed_ext)) {
                // Generate unique filename
                $new_file_name = uniqid('event_', true) . '.' . $file_ext;
                $upload_path = $upload_dir . $new_file_name;
                if (move_uploaded_file($file_tmp, $upload_path)) {
                    $image_path = 'assets/uploads/events/' . $new_file_name; // relative path for DB
                } else {
                    $error = 'Failed to upload image.';
                }
            } else {
                $error = 'Only JPG, JPEG, PNG, GIF, WEBP files are allowed.';
            }
        }

        if (empty($error)) {
            $stmt = $pdo->prepare("INSERT INTO events (title, description, event_date, image_path) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $description, $event_date, $image_path]);
            $success = 'Event posted successfully.';
        }
    }
}
?>
<?php include '../includes/header.php'; ?>

<h1>📅 Post New Event</h1>

<?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="success"><?= $success ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="form-container">
    <div class="form-group">
        <label for="title">Event Title *</label>
        <input type="text" name="title" id="title" required>
    </div>
    <div class="form-group">
        <label for="description">Description *</label>
        <textarea name="description" id="description" rows="5" required></textarea>
    </div>
    <div class="form-group">
        <label for="event_date">Event Date (optional)</label>
        <input type="date" name="event_date" id="event_date">
    </div>
    <div class="form-group">
        <label for="event_image">Event Photo (optional)</label>
        <input type="file" name="event_image" id="event_image" accept="image/*">
    </div>
    <button type="submit" class="btn">Post Event</button>
    <a href="dashboard.php" class="btn btn-outline">Cancel</a>
</form>

<?php include '../includes/footer.php'; ?>