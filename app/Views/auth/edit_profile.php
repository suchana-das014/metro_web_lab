<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Profile</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<div class="max-w-xl mx-auto bg-white shadow p-6 mt-10 rounded-lg">

    <h2 class="text-2xl font-bold mb-4">Edit Profile</h2>

    <form action="/edit-profile/update" method="POST" enctype="multipart/form-data">

        <!-- USERNAME -->
        <label class="block font-semibold mb-1">Username</label>
<input 
    type="text" 
    name="username" 
    value="<?= htmlspecialchars($user['username']) ?>" 
    class="w-full p-2 border rounded mb-4" 
    required
>


        <!-- BIO -->
        <label class="block font-semibold mb-1">Bio</label>
        <textarea name="bio" rows="4" class="w-full p-2 border rounded mb-4"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>

        <!-- PROFILE PICTURE -->
        <label class="block font-semibold mb-1">Profile Picture</label>
        <input type="file" name="profile_picture" class="mb-4">

        <!-- Preview -->
        <img src="/<?= htmlspecialchars($user['profile_picture'] ?? 'uploads/profile/default.png') ?>"
             class="w-24 h-24 rounded-full mb-4 object-cover border">

        <!-- SUBMIT BUTTON -->
        <button type="submit"
                class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700">
            Save Changes
        </button>

    </form>

</div>

</body>
</html>
