<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Feed</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  <div class="flex justify-between items-center p-4 bg-blue-600 text-white">
    <h1 class="text-xl font-bold">MySocial</h1>
    <div>
      <a href="/profile" class="text-white hover:underline">My Profile</a>
      <a href="/logout" class="ml-4 text-white hover:underline">Logout</a>
    </div>
  </div>

  <div class="max-w-2xl mx-auto mt-8 bg-white p-6 rounded-lg shadow">
    <form action="/create_post" method="POST" enctype="multipart/form-data">
      <textarea name="content" rows="3" placeholder="What's on your mind?"
        class="w-full border rounded p-2 mb-3 focus:outline-none focus:ring"></textarea>
      <input type="file" name="image" class="mb-3">
      <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Post</button>
    </form>
  </div>

  <div class="max-w-2xl mx-auto mt-6 space-y-4">
    <?php foreach ($posts as $post): ?>
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="font-semibold text-blue-600">
          <?= htmlspecialchars($post['username']) ?>
        </div>
        <?php if (!empty($post['content'])): ?>
          <p class="mt-2 text-gray-800"><?= nl2br(htmlspecialchars($post['content'])) ?></p>
        <?php endif; ?>
        <?php if (!empty($post['image'])): ?>
          <img src="/<?= htmlspecialchars($post['image']) ?>" class="mt-3 rounded-lg">
        <?php endif; ?>
        <p class="text-gray-500 text-sm mt-1"><?= htmlspecialchars($post['created_at']) ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</body>
</html>
