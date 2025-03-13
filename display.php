<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$servername = "localhost";
$username = "root";
$password = "";
$database = "notesdb"; // Change to your database name

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
$user_id = $_SESSION['user_id'];

// Retrieve user's tasks, ordering by status and due date
$task_sql = "SELECT * FROM tasks WHERE user_id = ? ORDER BY FIELD(status, 'pending', 'in-progress', 'completed'), due_date ASC";
$task_stmt = $conn->prepare($task_sql);
$task_stmt->bind_param("i", $user_id);
$task_stmt->execute();
$task_result = $task_stmt->get_result();

// Retrieve user's notes
$note_sql = "SELECT * FROM notes WHERE user_id = ? ORDER BY created_at DESC";
$note_stmt = $conn->prepare($note_sql);
$note_stmt->bind_param("i", $user_id);
$note_stmt->execute();
$note_result = $note_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/frappe-gantt/dist/frappe-gantt.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow-lg mb-6">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <h1 class="text-xl font-bold text-gray-800">My Productivity Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="logout.php" class="text-red-500 hover:text-red-600">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Your Tasks</h2>
                <a href="add_task.php" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200">
                    Add Task
                </a>
            </div>
            <?php if ($task_result->num_rows > 0): ?>
                <ul class="space-y-4">
                    <?php while ($task = $task_result->fetch_assoc()): ?>
                        <li class="border rounded-lg p-4 bg-gray-50 hover:shadow-md transition duration-200">
                            <div class="flex justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($task['title']); ?></h3>
                                    <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($task['description'])); ?></p>
                                    <p class="text-sm text-gray-500">Due: <?php echo date('F j, Y, g:i a', strtotime($task['due_date'])); ?></p>
                                </div>
                                <span class="flex items-center space-x-2">
                                    <span class="w-3 h-3 rounded-full inline-block 
                                        <?php echo ($task['status'] == 'completed') ? 'bg-green-500' : (($task['status'] == 'in-progress') ? 'bg-yellow-500' : 'bg-red-500'); ?>">
                                    </span>
                                    <span class="text-sm text-gray-800">
                                        <?php echo ucfirst($task['status']); ?>
                                    </span>
                                </span>
                            </div>
                            <div class="flex space-x-2 mt-2">
                                <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600 transition duration-200">Edit</a>
                                <?php if ($task['status'] != 'completed'): ?>
                                    <button onclick="markTaskComplete(<?php echo $task['id']; ?>)" class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600 transition duration-200">Mark as Complete</button>
                                <?php endif; ?>
                                <?php if ($task['status'] == 'completed'): ?>
                                    <button onclick="deleteTask(<?php echo $task['id']; ?>)" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600 transition duration-200">Delete</button>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p class="text-gray-600">No tasks added yet.</p>
            <?php endif; ?>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Your Notes</h2>
                <a href="index.php" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200">
                    Add Note
                </a>
            </div>
            <?php if ($note_result->num_rows > 0): ?>
                <div class="space-y-4">
                    <?php while ($note = $note_result->fetch_assoc()): ?>
                        <div class="border rounded-lg p-4 bg-gray-50 hover:shadow-md transition duration-200">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($note['title']); ?></h3>
                                <div class="flex space-x-2">
                                    <a href="edit.php?id=<?php echo $note['id']; ?>" 
                                       class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600 transition duration-200">
                                        Edit
                                    </a>
                                    <button onclick="deleteNote(<?php echo $note['id']; ?>)" 
                                            class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600 transition duration-200">
                                        Delete
                                    </button>
                                </div>
                            </div>
                            <p class="text-gray-700 whitespace-pre-wrap"><?php echo nl2br(htmlspecialchars($note['content'])); ?></p>
                            <div class="mt-3 text-sm text-gray-500">
                                Created: <?php echo date('F j, Y, g:i a', strtotime($note['created_at'])); ?>
                                <?php if ($note['updated_at'] !== null): ?>
                                    <br>
                                    Updated: <?php echo date('F j, Y, g:i a', strtotime($note['updated_at'])); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-600">No notes created yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/frappe-gantt/dist/frappe-gantt.min.js"></script>
    <script>
        function deleteNote(noteId) {
            if (confirm('Are you sure you want to delete this note?')) {
                fetch('delete.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'note_id=' + noteId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting note: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting note');
                });
            }
        }

        function deleteTask(taskId) {
            if (confirm('Are you sure you want to delete this task?')) {
                fetch('delete_task.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'task_id=' + taskId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting task: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting task');
                });
            }
        }

        function markTaskComplete(taskId) {
            fetch('mark_complete.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'task_id=' + taskId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error marking task as complete: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error marking task as complete');
            });
        }

        const tasks = [
            <?php while ($task = $task_result->fetch_assoc()): ?>
            {
                id: '<?php echo $task['id']; ?>',
                name: '<?php echo htmlspecialchars($task['title']); ?>',
                start: '<?php echo date('Y-m-d', strtotime($task['due_date'])); ?>',
                end: '<?php echo date('Y-m-d', strtotime($task['due_date'])); ?>',
                progress: <?php echo ($task['status'] == 'completed') ? 100 : 0; ?>,
                dependencies: ''
            },
            <?php endwhile; ?>
        ];

        const gantt = new Gantt("#gantt", tasks, {
            view_mode: 'Day',
            date_format: 'YYYY-MM-DD',
            custom_popup_html: function(task) {
                return `
                    <div class="details-container">
                        <h5>${task.name}</h5>
                        <p>Due: ${task.end}</p>
                    </div>
                `;
            }
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>