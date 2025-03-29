<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DigiKhata - Home</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            font-size: 18px; 
            text-align: center;
        }

        header {
            background-color: rgb(0, 0, 0);
            color: white;
            padding: 20px 0;
            display: flex;
            justify-content: center; /* Centering header text */
            align-items: center;
        }

        h1 {
            margin: 0;
        }

        nav {
            margin-top: 10px;
        }

        nav a {
            margin: 0 15px;
            text-decoration: none;
            color: white;
            font-weight: bold;
            border: 2px solid transparent;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        nav a:hover {
            border: 2px solid white;
            background-color: rgba(255, 255, 255, 0.2);
        }

        .container {
            flex-grow: 1; 
            display: flex; /* Use flexbox to center the content */
            flex-direction: column; /* Stack elements vertically */
            justify-content: center; /* Center elements vertically */
            align-items: center; /* Center elements horizontally */
            margin-top: 50px; /* Space from header */
        }

        .button {
            display: inline-block;
            padding: 10px 20px; 
            font-size: 18px; 
            margin: 10px;
            color: white;
            background-color: rgb(255, 0, 0);
            border: none;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: rgb(136, 33, 33);
        }

        .footer {
            background-color: rgb(0, 0, 0);
            color: white;
            padding: 15px 0;
            width: 100%;
            text-align: center;
            bottom: 0;
        }
    </style>
</head>
<body>

    <header>
        <h1>Welcome to DigiKhata</h1>
    </header>

    <div class="container">
        <h2>Your Digital Cashbook</h2>
        <p>Manage your finances easily and efficiently with DigiKhata.</p>
        <a href="login.php" class="button">Get Started</a>
        <a href="register.php" class="button">Create an Account</a>
        <a href="login.php" class="button">View Dashboard</a> <!-- Updated link to login.php -->
    </div>

    <div class="footer">
        <p>&copy; 2025 DigiKhata. All rights reserved.</p>
    </div>

</body>
</html>
