<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "notesdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $task_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    $task_sql = "SELECT * FROM tasks WHERE id = ? AND user_id = ?";
    $task_stmt = $conn->prepare($task_sql);
    $task_stmt->bind_param("ii", $task_id, $user_id);
    $task_stmt->execute();
    $task_result = $task_stmt->get_result();

    if ($task_result->num_rows > 0) {
        $task = $task_result->fetch_assoc();
    } else {
        echo "Task not found.";
        exit();
    }
} else {
    echo "Invalid task ID.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $due_date = $_POST['due_date'];
    $status = $_POST['status'];

    $update_sql = "UPDATE tasks SET title = ?, description = ?, due_date = ?, status = ? WHERE id = ? AND user_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssii", $title, $description, $due_date, $status, $task_id, $user_id);

    if ($update_stmt->execute()) {
        header("Location: display.php");
        exit();
    } else {
        echo "Error updating task: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-lg rounded-lg p-8 max-w-lg w-full">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Edit Task</h2>
        <form action="edit_task.php?id=<?php echo $task_id; ?>" method="POST" class="space-y-4">
            <div>
                <label for="title" class="block text-gray-700 font-medium">Title:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required 
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label for="description" class="block text-gray-700 font-medium">Description:</label>
                <textarea id="description" name="description" 
                          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400"><?php echo htmlspecialchars($task['description']); ?></textarea>
            </div>

            <div>
                <label for="due_date" class="block text-gray-700 font-medium">Due Date:</label>
                <input type="datetime-local" id="due_date" name="due_date" value="<?php echo date('Y-m-d\TH:i', strtotime($task['due_date'])); ?>" 
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label for="status" class="block text-gray-700 font-medium">Status:</label>
                <select id="status" name="status" 
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400">
                    <option value="pending" <?php echo ($task['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="in-progress" <?php echo ($task['status'] == 'in-progress') ? 'selected' : ''; ?>>In Progress</option>
                    <option value="completed" <?php echo ($task['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                </select>
            </div>

            <button type="submit" 
                    class="w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200">
                Update Task
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="display.php" class="text-blue-500 hover:underline">Go Back to Tasks</a>
        </div>
    </div>
</body>
</html>