<?php
include 'dbConfig.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $type = $_POST['type'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $amount = $_POST['amount'];
    $details = $_POST['details'];
    $datetime = $date . ' ' . $time;

    // Validate inputs
    if (empty($type) || empty($date) || empty($time) || empty($amount)) {
        die("All fields are required!");
    }

    // Validate 'type' value
    $valid_types = ['income', 'expense'];
    if (!in_array($type, $valid_types)) {
        die("Invalid type value. Must be 'income' or 'expense'.");
    }

    // Fetch current balance
    $balance_query = $conn->prepare("SELECT SUM(CASE WHEN type = 'income' THEN amount ELSE -amount END) AS balance FROM transactions WHERE user_id = ?");
    $balance_query->bind_param("i", $user_id);
    $balance_query->execute();
    $result = $balance_query->get_result();
    $row = $result->fetch_assoc();
    $current_balance = $row['balance'] ?? 0; // Default to 0 if no transactions

    // Prevent negative balance
    if ($type == 'expense' && $amount > $current_balance) {
        die("Error: Insufficient balance to cash out!");
    }

    // Prepare SQL query
    $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, details, created_at) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isdss", $user_id, $type, $amount, $details, $datetime);

    // Execute query
    if ($stmt->execute()) {
        header("Location: dashboard.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
