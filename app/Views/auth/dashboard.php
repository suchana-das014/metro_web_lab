<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Feed</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = $_SESSION['user'] ?? null;
?>

  <!-- Header -->
  <div class="flex justify-between items-center p-4 bg-blue-600 text-white">
    <h1 class="text-xl font-bold">MySocial</h1>
    <div>
      <a href="/my-profile" class="text-white hover:underline">My Profile</a>
      <a href="/logout" class="ml-4 text-white hover:underline">Logout</a>
    </div>
  </div>

  <!-- Post form -->
  <div class="max-w-2xl mx-auto mt-8 bg-white p-6 rounded-lg shadow">
    <form action="/create_post" method="POST" enctype="multipart/form-data">
      <textarea name="content" rows="3" placeholder="What's on your mind?"
        class="w-full border rounded p-2 mb-3 focus:outline-none focus:ring"></textarea>
      <input type="file" name="image" class="mb-3">
      <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Post</button>
    </form>
  </div>

  <!-- Feed -->
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

        <!-- Like -->
        <div class="mt-3 flex items-center space-x-4">
          <form class="like-form inline" data-post-id="<?= $post['id'] ?>">
            <button type="button" class="like-btn text-blue-500 hover:text-blue-700">
              ❤️ Like (<span class="like-count"><?= \App\Models\Post::countLikes($post['id']) ?></span>)
            </button>
          </form>
        </div>

        <!-- Comments -->
        <div class="mt-3">
          <div class="comments" id="comments-<?= $post['id'] ?>">
            <?php foreach (\App\Models\Post::getComments($post['id']) as $c): ?>
              <div class="flex justify-between bg-gray-100 p-2 rounded mt-1 text-sm">
                <span>
                  <b class="text-blue-600"><?= htmlspecialchars($c['username']) ?>:</b>
                  <?= htmlspecialchars($c['comment']) ?>
                </span>
                <?php if ($user && $c['user_id'] == $user['id']): ?>
                  <button 
                    class="delete-comment text-red-500" 
                    data-comment-id="<?= $c['id'] ?>" 
                    data-post-id="<?= $post['id'] ?>">✖</button>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>

          <form class="comment-form flex mt-2" data-post-id="<?= $post['id'] ?>">
            <input type="text" name="comment" placeholder="Write a comment..."
                   class="border p-2 flex-grow rounded text-sm bg-white focus:ring focus:ring-blue-200" required>
            <button type="button" class="comment-btn bg-blue-500 text-white px-3 rounded ml-1">Post</button>
          </form>
        </div>

        <p class="text-gray-500 text-sm mt-1"><?= htmlspecialchars($post['created_at']) ?></p>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Like + Comment JS -->
  <script>
  // LIKE (no page reload)
  document.querySelectorAll('.like-btn').forEach(button => {
    button.addEventListener('click', async () => {
      const form = button.closest('.like-form');
      const postId = form.dataset.postId;

      try {
        const response = await fetch('/like', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'post_id=' + encodeURIComponent(postId)
        });

        if (response.ok) {
          const count = await response.text();
          form.querySelector('.like-count').textContent = count;
        }
      } catch (err) {
        console.error('Like failed:', err);
      }
    });
  });

  // COMMENT (instant add, no refresh)
  document.querySelectorAll('.comment-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
      const form = btn.closest('.comment-form');
      const postId = form.dataset.postId;
      const input = form.querySelector('input[name="comment"]');
      const comment = input.value.trim();
      if (!comment) return;

      try {
        const response = await fetch('/comment', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'post_id=' + encodeURIComponent(postId) + '&comment=' + encodeURIComponent(comment)
        });

        if (response.ok) {
          const html = await response.text();
          document.getElementById('comments-' + postId).innerHTML = html;
          input.value = '';
        } else if (response.status === 403) {
          alert("Session expired. Please log in again.");
          window.location.href = "/login";
        }
      } catch (err) {
        console.error('Comment failed:', err);
      }
    });
  });

  // DELETE COMMENT (instant remove)
  document.addEventListener('click', async e => {
    if (e.target.classList.contains('delete-comment')) {
      const commentId = e.target.dataset.commentId;
      const postId = e.target.dataset.postId;

      try {
        const res = await fetch('/delete_comment', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'comment_id=' + encodeURIComponent(commentId) + '&post_id=' + encodeURIComponent(postId)
        });

        if (res.ok) {
          const html = await res.text();
          document.getElementById('comments-' + postId).innerHTML = html;
        } else if (res.status === 403) {
          alert("Session expired. Please log in again.");
          window.location.href = "/login";
        }
      } catch (err) {
        console.error('Delete comment failed:', err);
      }
    }
  });
  </script>
</body>
</html>