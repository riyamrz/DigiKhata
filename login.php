<?php
include 'dbConfig.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, full_name, password, shop_name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $full_name, $hashed_password, $shop_name);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['full_name'] = $full_name;
        $_SESSION['shop_name'] = $shop_name;
        header("Location: dashboard.php");
    } else {
        echo "<p class='error'>Invalid email or password</p>";
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
    <title>Login - DigiKhata</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 12px; /* Consistent padding */
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box; /* Include padding and border in total width */
        }

        .btn {
            width: 100%; /* Make button full width */
            padding: 12px; /* Same padding as input fields */
            margin-top: 10px; /* Margin above buttons */
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            display: flex; /* Enable flexbox for centering text */
            align-items: center; /* Center text vertically */
            justify-content: center; /* Center text horizontally */
            height: 45px; /* Set a fixed height for consistency */
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
            height: 45px; /* Match height with the login button */
            line-height: 45px; /* Center text vertically */
            display: flex; /* Enable flexbox for centering text */
            align-items: center; /* Center text vertically */
            justify-content: center; /* Center text horizontally */
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
    <div class="login-container">
        <h2>Login to DigiKhata</h2>
        <form method="post" action="login.php">
            <input type="email" name="email" placeholder="Email" required class="form-control">
            <input type="password" name="password" placeholder="Password" required class="form-control">
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <a href="register.php" class="btn btn-secondary">Don't have an account? Register</a>
    </div>
</body>
</html>
