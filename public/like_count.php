<?php
require __DIR__ . '/../vendor/autoload.php';
use App\Models\Post;

$postId = (int)($_GET['post_id'] ?? 0);
if ($postId > 0) {
    echo Post::countLikes($postId);
}
