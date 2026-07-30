<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploaded Notices in Database</title>
    <style>
        body {
            background-color: #f0f4f8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
        }

        .header {
            background-color: #093f82;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
        }

        .button-group {
            display: flex;
            gap: 10px;
        }

        .ctn {
            padding: 8px 16px;
            background-color: #4082d2;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .ctn:hover {
            background-color: #2d6db5;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .container {
            width: 90%;
            max-width: 600px;
            margin: 30px auto;
        }
        .notice-card {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 20px;
  padding: 15px;
  background-color: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
  flex-wrap: wrap;
}

.notice-text {
  flex: 1;
  min-width: 250px;
}

.notice-image-wrapper {
  max-width: 200px;
  flex-shrink: 0;
}

.notice-image {
  width: 100%;
  height: auto;
  border-radius: 8px;
}

.delete-btn {
  margin-top: 10px;
  padding: 8px 12px;
  background-color: #dc3545;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.delete-btn:hover {
  background-color: #c82333;
}

    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome Server User</h1>
        <div class="button-group">
            <button class="ctn" onclick="window.location.href='after_login_server.php'">Upload Notice</button>
            <button class="ctn" onclick="window.location.href='loginServer.php'">Logout</button>
        </div>
    </div>

    <div class="container">
        <h2>Uploaded Notices</h2>
        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        $conn = mysqli_connect("localhost", "root", "", "notice");
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        if (isset($_POST['delete_id'])) {
            $id = intval($_POST['delete_id']);
            $result = mysqli_query($conn, "SELECT image FROM notice_table WHERE id = $id");
            $row = mysqli_fetch_assoc($result);

            if ($row) {
                $imagePath = "uploads/" . $row['image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                mysqli_query($conn, "DELETE FROM notice_table WHERE id = $id");
                echo "<p style='color: green; text-align: center;'>✅ Notice deleted successfully.</p>";
            } else {
                echo "<p style='color: red; text-align: center;'>❌ Notice not found.</p>";
            }
        }

        $result = mysqli_query($conn, "SELECT * FROM notice_table ORDER BY id ASC");

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<div class='notice-card'>";
            
                    // Text Section
                    echo "<div class='notice-text'>";
                        echo "<h3><strong>Department:</strong> " . nl2br(htmlspecialchars($row['department'])) . "</h3>";
                        echo "<h4>" . htmlspecialchars($row['title']) . "</h4>";
                        echo "<p><strong>Uploaded on:</strong> " . date("F j, Y, g:i A", strtotime($row['created_at'])) . "</p>";
                        echo "<form method='post' onsubmit='return confirm(\"Are you sure you want to delete this notice?\")'>";
                            echo "<input type='hidden' name='delete_id' value='" . $row['id'] . "'>";
                            echo "<input type='submit' value='Delete' class='delete-btn'>";
                        echo "</form>";
                    echo "</div>"; // end notice-text
            
                    // Image Section
                    echo "<div class='notice-image-wrapper'>";
                        echo "<img src='uploads/" . htmlspecialchars($row['image']) . "' alt='Notice Image' class='notice-image'>";
                    echo "</div>";
            
                echo "</div>"; // end notice-card
            }
            
        } else {
            echo "<p style='color:red; text-align:center;'>❌ Error fetching notices: " . mysqli_error($conn) . "</p>";
        }

        mysqli_close($conn);
        ?>
    </div>
</body>
</html>