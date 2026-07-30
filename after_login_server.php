<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Notice Submission</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #e3f2fd, #ffffff);
            color: #333;
        }

        .header {
            background-color: #0d47a1;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
        }

        .header-buttons button {
            background-color: #1976d2;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 6px;
            margin-left: 10px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .header-buttons button:hover {
            background-color: #1565c0;
        }

        .form-container {
            max-width: 600px;
            background-color: white;
            margin: 40px auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: 600;
        }

        input[type="text"],
        input[type="date"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }

        input[type="submit"] {
            width: 100%;
            background-color: #0d47a1;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            margin-top: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0b3c91;
        }

        .message {
            text-align: center;
            margin-top: 20px;
            font-size: 18px;
        }
    </style>
</head>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$server = "localhost";
$username = "root";
$password = "";
$dbname = "notice";

$conn = mysqli_connect($server, $username, $password, $dbname);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit']) && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);

    $targetDir = "uploads/";
    $fileName = basename($_FILES["image"]["name"]);
    $uniqueName = time() . '_' . $fileName;
    $targetFilePath = $targetDir . $uniqueName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    $allowedTypes = ["jpg", "png", "jpeg", "gif"];
    if (!in_array(strtolower($fileType), $allowedTypes)) {
        $message = "<p style='color: red;'>❌ Only JPG, PNG, and GIF files are allowed.</p>";
    } elseif (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
        $sql = "INSERT INTO notice_table ( `department`,`title`, `image`,`created_at`) VALUES ('$department', '$title', '$uniqueName',NOW())";

        if (mysqli_query($conn, $sql)) {
            $message = "<p style='color: green;'>✅ Data submitted successfully!</p>";
        } else {
            $message = "<p style='color: red;'>❌ Database Error: " . mysqli_error($conn) . "</p>";
        }
    } else {
        $message = "<p style='color: red;'>❌ Error uploading file.</p>";
    }
}

mysqli_close($conn);
?>
<body>
    <div class="header">
        <h1>Server Notice Panel</h1>
        <div class="header-buttons">
            <button onclick="location.href='Uploaded_Notices.php'">Dashboard</button>
            <button onclick="location.href='loginServer.php'">Logout</button>
        </div>
    </div>

    <div class="form-container">
        <h2>Submit a New Notice</h2>
        <form action="#" method="post" enctype="multipart/form-data">
            
            <label for="department">Department</label>
            <input type="text" id="department" name="department"  required>

            <label for="title">Title</label>
            <input type="text" id="title" name="title" required>

            <label for="image">Upload Notice Image</label>
            <input type="file" id="image" name="image" accept="image/*" required>

            <input type="submit" name="submit" value="Submit">
        </form>
        <div class="message">
            <?php echo $message; ?>
        </div>
    </div>
</body>
</html>