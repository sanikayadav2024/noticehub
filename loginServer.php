<?php
session_start();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "notice";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get input from form
    $email = $_POST['email'] ?? '';
    $user_password = $_POST['password'] ?? '';

    // Prevent empty login
    if (empty($email) || empty($user_password)) {
        $error = "Please enter both email and password.";
    } else {
        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM serveruser WHERE email = ? AND password = ?");
        $stmt->bind_param("ss", $email, $user_password);
        $stmt->execute();
        $result = $stmt->get_result();

        // If user found
        if ($result->num_rows > 0) {
            header("Location: after_login_server.php");
            exit();
        } else {
            $error = "Invalid email or password.";
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authority Login Page</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
        }

        .login-box {
            width: 300px;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            margin-bottom: 5px;
            color: #333;
        }

        input {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
        }
      
        button {
            width: 100px;
            background-color: #ff6600;
            color: #fff;
            border: 2px solid #cc5200;
            border-radius: 5px;
            padding: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #cc5200;
        }

        .error {
            color: red;
            margin-bottom: 10px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="login-box">
        <form method="POST" action="LoginServer.php">
            <?php if (!empty($error)) : ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</body>

</html>
