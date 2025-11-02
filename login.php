<?php
header('Content-Type: application/json');
session_start();

// Include database configuration
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate input
    if (empty($username) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Username and password are required']);
        exit;
    }

    // Fetch user from database
    $user = $conn->prepare("SELECT id, username, password, first_name FROM users WHERE username = ?");
    $user->bind_param("s", $username);
    $user->execute();
    $result = $user->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid username or password']);
        $user->close();
        $conn->close();
        exit;
    }

    $row = $result->fetch_assoc();
    $user->close();

    // Verify password
    if (password_verify($password, $row['password'])) {
        // Set session
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['first_name'] = $row['first_name'];

        echo json_encode(['status' => 'success', 'message' => 'Login successful!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid username or password']);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
