<?php
include 'dbConfig.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $transaction_id = $_POST['transaction_id'];

    // Delete the specific transaction
    $stmt = $conn->prepare("DELETE FROM transactions WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $transaction_id, $user_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    header("Location: dashboard.php");
    exit();
}
?>