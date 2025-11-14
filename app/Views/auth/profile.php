<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$user  = $user  ?? [];
$posts = $posts ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($user['name']) ?> | Profile</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-gray-100">

<div class="max-w-2xl mx-auto bg-white shadow p-6 mt-10 rounded-lg">

    <div class="flex items-center space-x-6">

        <!-- PROFILE PICTURE -->
        <img src="/<?= htmlspecialchars($user['profile_picture'] ?? 'uploads/profile/default.png') ?>"
             class="w-24 h-24 rounded-full object-cover border shadow-sm">

        <div class="flex flex-col">
            <!-- USERNAME (BIG + BOLD + FIXED) -->
            <h2 class="text-3xl font-bold text-gray-900">
                <?= htmlspecialchars($user['username']) ?>

            </h2>

            <!-- BIO -->
            <?php if (!empty($user['bio'])): ?>
                <p class="mt-2 text-gray-800">
                    <?= nl2br(htmlspecialchars($user['bio'])) ?>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- EDIT PROFILE BUTTON -->
    <a href="/edit-profile"
       class="mt-5 inline-block bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition">
        Edit Profile
    </a>

    <hr class="my-6">

    <h3 class="text-xl font-semibold mb-4">Posts</h3>

    <?php foreach ($posts as $post): ?>
        <div class="bg-gray-100 p-4 rounded-lg mb-6 shadow-sm">

            <p class="text-gray-900 whitespace-pre-line">
                <?= nl2br(htmlspecialchars($post['content'])) ?>
            </p>

            <?php if (!empty($post['image'])): ?>
                <img src="/<?= htmlspecialchars($post['image']) ?>" class="mt-3 rounded-lg shadow">
            <?php endif; ?>

            <div class="flex justify-between text-sm text-gray-600 mt-3">
                <span class="like-count-<?= $post['id'] ?>">❤️ <?= $post['like_count'] ?> Likes</span>
                <span>💬 <?= $post['comment_count'] ?> Comments</span>
            </div>

            <button class="like-btn mt-2 text-blue-600"
                    data-post-id="<?= $post['id'] ?>">Like</button>

            <input type="text"
                class="comment-input border p-2 w-full rounded text-sm mt-3"
                placeholder="Write a comment..."
                data-post-id="<?= $post['id'] ?>">

            <div class="comments mt-3" id="comments-<?= $post['id'] ?>"></div>

        </div>
    <?php endforeach; ?>

</div>

<script>
// LIKE BUTTON
$(".like-btn").click(function () {
    let postId = $(this).data("post-id");

    $.post("/like", { post_id: postId }, function (newCount) {
        $(".like-count-" + postId).text("❤️ " + newCount + " Likes");
    });
});

// COMMENT SUBMISSION
$(".comment-input").keypress(function (e) {
    if (e.which === 13) {
        let postId = $(this).data("post-id");
        let comment = $(this).val().trim();
        if (comment === "") return;

        $.post("/comment", { post_id: postId, comment: comment }, function (html) {
            $("#comments-" + postId).html(html);
        });

        $(this).val("");
    }
});
</script>

</body>
</html>
