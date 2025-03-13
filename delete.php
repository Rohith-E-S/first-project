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
       echo json_encode(['success' => false, 'message' => 'User not logged in']);
       exit();
   }
   
   $user_id = $_SESSION['user_id']; // Get logged-in user's ID
   
   // Check if note_id is set
   if (isset($_POST['note_id'])) {
       $note_id = $_POST['note_id'];
   
       // Prepare and execute the delete statement
       $sql = "DELETE FROM notes WHERE id = ? AND user_id = ?";
       $stmt = $conn->prepare($sql);
       $stmt->bind_param("ii", $note_id, $user_id);
   
       if ($stmt->execute()) {
           echo json_encode(['success' => true]);
       } else {
           echo json_encode(['success' => false, 'message' => 'Error deleting note']);
       }
   
       $stmt->close();
   } else {
       echo json_encode(['success' => false, 'message' => 'Note ID not provided']);
   }
   
   $conn->close();
   ?>