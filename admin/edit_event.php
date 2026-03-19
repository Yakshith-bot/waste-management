<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotAdmin();

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$event) {
    header('Location: view_events.php');
    exit;
}

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $event_date = !empty($_POST['event_date']) ? $_POST['event_date'] : null;
    $remove_image = isset($_POST['remove_image']);

    if (empty($title) || empty($description)) {
        $error = 'Title and description are required.';
    } else {
        $image_path = $event['image_path']; // keep old by default

        // Handle new image upload
        if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../assets/uploads/events/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

            $file_tmp = $_FILES['event_image']['tmp_name'];
            $file_name = $_FILES['event_image']['name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array($file_ext, $allowed_ext)) {
                // Delete old image if exists
                if ($image_path && file_exists('../' . $image_path)) {
                    unlink('../' . $image_path);
                }
                $new_file_name = uniqid('event_', true) . '.' . $file_ext;
                $upload_path = $upload_dir . $new_file_name;
                if (move_uploaded_file($file_tmp, $upload_path)) {
                    $image_path = 'assets/uploads/events/' . $new_file_name;
                } else {
                    $error = 'Failed to upload image.';
                }
            } else {
                $error = 'Invalid image format.';
            }
        }

        // Remove image if checkbox checked
        if ($remove_image && $image_path) {
            if (file_exists('../' . $image_path)) {
                unlink('../' . $image_path);
            }
            $image_path = null;
        }

        if (empty($error)) {
            $update = $pdo->prepare("UPDATE events SET title = ?, description = ?, event_date = ?, image_path = ? WHERE id = ?");
            $update->execute([$title, $description, $event_date, $image_path, $id]);
            $success = 'Event updated successfully.';
            // Refresh event data
            $stmt->execute([$id]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
}
?>
<?php include '../includes/header.php'; ?>

<h1>✏️ Edit Event</h1>

<?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="success"><?= $success ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="form-container">
    <div class="form-group">
        <label for="title">Event Title *</label>
        <input type="text" name="title" id="title" value="<?= htmlspecialchars($event['title']) ?>" required>
    </div>
    <div class="form-group">
        <label for="description">Description *</label>
        <textarea name="description" id="description" rows="5" required><?= htmlspecialchars($event['description']) ?></textarea>
    </div>
    <div class="form-group">
        <label for="event_date">Event Date (optional)</label>
        <input type="date" name="event_date" id="event_date" value="<?= $event['event_date'] ?>">
    </div>

    <?php if ($event['image_path']): ?>
        <div style="margin-bottom: 1rem;">
            <p><strong>Current Image:</strong></p>
            <img src="<?= BASE_URL . $event['image_path'] ?>" style="max-width: 200px; max-height: 150px; border-radius: 8px;">
            <label style="display: block; margin-top: 0.5rem;">
                <input type="checkbox" name="remove_image" value="1"> Remove this image
            </label>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <label for="event_image">Upload New Image (optional)</label>
        <input type="file" name="event_image" id="event_image" accept="image/*">
    </div>

    <button type="submit" class="btn">Update Event</button>
    <a href="view_events.php" class="btn btn-outline">Cancel</a>
</form>

<?php include '../includes/footer.php'; ?>