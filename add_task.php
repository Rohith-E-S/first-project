<?php
session_start();
$servername = "localhost";
$username = "root"; // Update if needed
$password = ""; // Update if needed
$dbname = "notesdb"; // Change to your actual database name

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $due_date = $_POST['due_date'];
    $status = $_POST['status'];

    $sql = "INSERT INTO tasks (user_id, title, description, due_date, status) 
            VALUES ('$user_id', '$title', '$description', '$due_date', '$status')";

    if ($conn->query($sql) === TRUE) {
        header("Location: display.php"); // Redirect to task display page
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Task</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
        <div class="bg-white shadow-lg rounded-lg p-8 max-w-lg w-full">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Add New Task</h2>
            <form action="add_task.php" method="POST" class="space-y-4">
                <div>
                    <label for="title" class="block text-gray-700 font-medium">Title:</label>
                    <input type="text" id="title" name="title" required 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400">
                </div>

                <div>
                    <label for="description" class="block text-gray-700 font-medium">Description:</label>
                    <textarea id="description" name="description" 
                              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400"></textarea>
                </div>

                <div>
                    <label for="due_date" class="block text-gray-700 font-medium">Due Date:</label>
                    <input type="text" id="due_date" name="due_date" 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400">
                </div>

                <div>
                    <label for="status" class="block text-gray-700 font-medium">Status:</label>
                    <select id="status" name="status" 
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 mb-3">
                        <option value="pending">Pending</option>
                        <option value="in-progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <button type="submit" 
                        class="w-full bg-blue-500 text-white px-4 py-4 rounded-lg hover:bg-blue-600 transition duration-200">
                    Add Task
                </button>
            </form>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#due_date", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });
    </script>
</body>
</html>
