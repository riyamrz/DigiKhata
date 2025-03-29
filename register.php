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
    <title>Register - DigiKhata</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 22px;
            font-weight: bold;
        }

        input {
            width: 100%; /* Full width for input fields */
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box; /* Include padding and border in total width */
        }

        .btn {
            width: 100%; /* Full width for buttons */
            padding: 12px; /* Same padding as input fields */
            margin-top: 10px; /* Margin above buttons */
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-align: center;
            display: block; /* Make button a block element */
            box-sizing: border-box; /* Include padding and border in total width */
        }

        .btn-primary {
            background: rgb(255, 0, 0);
            color: white;
        }

        .btn-primary:hover {
            background: rgb(179, 0, 0);
        }

        .btn-secondary {
            background: rgb(0, 0, 0);
            color: white;
            margin-top: 10px; 
            text-decoration: none;
        }

        .btn-secondary:hover {
            background: rgb(19, 21, 22);
        }

        .error {
            color: red;
            font-size: 14px;
            margin: 10px 0;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Register</h2>
        <form method="post" action="register.php">
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <input type="text" name="shop_name" placeholder="Shop Name" required>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
        <a href="login.php" class="btn btn-secondary">Already have an account? Login</a>
    </div>

</body>
</html>
