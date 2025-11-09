<?php
$title = 'Create Post | AuthBoard';
ob_start();
?>

<h2>Create Post</h2>

<form method="POST" action="/post/create" class="form" enctype="multipart/form-data">
    <label for="content">Post your status</label>
    <textarea id="content" name="content" required></textarea>

    <label for="image">Add an image (optional)</label>
    <input type="file" id="image" name="image" accept="image/*">

    <button type="submit">Post</button>
</form>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
