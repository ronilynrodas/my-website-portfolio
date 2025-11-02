<?php
header('Content-Type: application/json');

// Include database configuration
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $email = $_POST['email'] ?? '';

    // Validate input
    if (empty($first_name) || empty($last_name) || empty($username) || empty($password) || empty($phone_number) || empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
        exit;
    }

    // Check if username or email already exists
    $check_user = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $check_user->bind_param("ss", $username, $email);
    $check_user->execute();
    $check_user->store_result();

    if ($check_user->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Username or email already exists']);
        $check_user->close();
        $conn->close();
        exit;
    }
    $check_user->close();

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into database
    $insert_user = $conn->prepare("INSERT INTO users (first_name, last_name, username, password, phone_number, email, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $insert_user->bind_param("ssssss", $first_name, $last_name, $username, $hashed_password, $phone_number, $email);

    if ($insert_user->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Signup successful! You can now login.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error creating account: ' . $conn->error]);
    }

    $insert_user->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
