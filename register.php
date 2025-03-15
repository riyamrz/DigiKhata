<?php
include 'dbConfig.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $shop_name = $_POST['shop_name'];

    // Validate inputs
    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password) || empty($shop_name)) {
        die("All fields are required!");
    }

    if ($password !== $confirm_password) {
        die("Passwords do not match!");
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL query
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, shop_name) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $full_name, $email, $hashed_password, $shop_name);

    // Execute query
    if ($stmt->execute()) {
        echo "<script>alert('Registration Successful!');</script>";
        echo "<script>window.location.href = 'login.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form method="post" action="register.php">
            <input type="text" name="full_name" placeholder="Full Name" required class="form-control mb-3">
            <input type="email" name="email" placeholder="Email" required class="form-control mb-3">
            <input type="password" name="password" placeholder="Password" required class="form-control mb-3">
            <input type="password" name="confirm_password" placeholder="Confirm Password" required class="form-control mb-3">
            <input type="text" name="shop_name" placeholder="Shop Name" required class="form-control mb-3">
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
        <a href="login.php" class="btn btn-secondary">Already have an account? Login</a>
    </div>
</body>
</html>