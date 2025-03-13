<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$servername = "localhost";
$username = "root"; 
$password = ""; 
$database = "notesdb";

// Connect to MySQL
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$note_id = isset($_GET['id']) ? $_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

// Fetch the note
$sql = "SELECT * FROM notes WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $note_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$note = $result->fetch_assoc();

// If note doesn't exist or doesn't belong to user
if (!$note) {
    header("Location: display.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Note</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow-lg mb-6">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <h1 class="text-xl font-bold text-gray-800">Edit Note</h1>
                <a href="display.php" class="text-blue-500 hover:text-blue-600">← Back to Notes</a>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <form action="update.php" method="POST" class="space-y-6">
                <input type="hidden" name="note_id" value="<?php echo $note['id']; ?>">
                
                <div>
                    <label for="title" class="block text-lg font-medium text-gray-700 mb-2">Title:</label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="<?php echo htmlspecialchars($note['title']); ?>" 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                           required>
                </div>
                
                <div>
 <label for="content" class="block text-lg font-medium text-gray-700 mb-2">Content:</label>
 <textarea id="content" 
   name="content" 
   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
   style="min-height: 400px;" 
   required><?php echo htmlspecialchars($note['content']); ?></textarea>
</div>
                
                <div class="flex gap-4">
                    <button type="submit" 
                            class="flex-1 bg-blue-500 text-white py-3 px-6 rounded-lg hover:bg-blue-600 transition duration-200 font-medium">
                        Save Changes
                    </button>
                    <a href="display.php" 
                       class="flex-1 bg-gray-500 text-white py-3 px-6 rounded-lg hover:bg-gray-600 transition duration-200 font-medium text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Auto-resize textarea as user types
    const textarea = document.getElementById('content');
    textarea.style.height = 'auto'; // Initially set height to auto
    textarea.addEventListener('input', function() {
        this.style.height = 'auto'; // Reset height to auto to shrink if necessary
        this.style.height = (this.scrollHeight) + 'px'; // Adjust the height according to content
    });
    </script>
</body>
</html>

<?php $conn->close(); ?>
