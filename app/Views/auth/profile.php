<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = $user ?? ($_SESSION['user'] ?? null);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  <div class="flex justify-between items-center p-4 bg-blue-600 text-white">
    <h1 class="text-xl font-bold">My Profile</h1>
    <a href="/dashboard" class="hover:underline">Back to Feed</a>
  </div>

  <div class="max-w-xl mx-auto mt-8 bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-semibold text-gray-800 mb-2">
      <?= htmlspecialchars($user['name'] ?? 'Unknown User') ?>
    </h2>
    <p class="text-gray-600 mb-1">
      <b>Email:</b> <?= htmlspecialchars($user['email'] ?? 'N/A') ?>
    </p>
    <p class="text-gray-600 mb-4">
      <b>Joined:</b> <?= htmlspecialchars($user['created_at'] ?? 'N/A') ?>
    </p>

    <h3 class="text-xl font-semibold mt-4 mb-2 text-blue-600">My Posts</h3>
    <?php if (!empty($posts)): ?>
      <?php foreach ($posts as $post): ?>
        <div class="border rounded p-3 mb-2 bg-gray-50">
          <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
          <?php if (!empty($post['image'])): ?>
            <img src="/<?= htmlspecialchars($post['image']) ?>" class="mt-2 rounded">
          <?php endif; ?>
          <p class="text-gray-400 text-sm mt-1"><?= htmlspecialchars($post['created_at']) ?></p>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-gray-500">You haven’t posted anything yet.</p>
    <?php endif; ?>
  </div>
</body>
</html>
