<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect if not logged in
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Notes</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow-lg mb-6">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="display.php"><h1 class="text-xl font-bold text-gray-800">My Productivity Dashboard</h1></a>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="logout.php" class="text-red-500 hover:text-red-600">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-4xl relative">
            <a href="display.php" class="absolute top-4 right-4 bg-red-500 text-white p-2 rounded hover:bg-gray-600 transition duration-200">Cancel</a>
            <h2 class="text-2xl font-bold mb-4">Create a Note</h2>
            <form action="process.php" method="POST">
                <label class="block mb-2">Title:</label>
                <input type="text" name="title" class="w-full p-4 border rounded mb-3" required>
                
                <label class="block mb-2">Content:</label>
                <textarea name="content" class="w-full p-4 border rounded mb-3" rows="10" required></textarea>
                
                <button type="submit" class="w-full bg-blue-500 text-white p-4 rounded hover:bg-blue-600">Save Note</button>
            </form>
        </div>
    </div>
</body>
</html>