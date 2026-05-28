<?php
session_start();
$conn = new mysqli("localhost", "root", "", "db_socialmedia");

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("❌ Please log in to create a group.");
}

$userId = $_SESSION['user_id']; // User ID from session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $groupName = trim($_POST["group_name"]);
    $groupDescription = trim($_POST["group_description"]);
    $uploadDir = "uploads/groups/";

    // Create upload directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $groupImage = "";

    // Handle Image Upload
    if (!empty($_FILES["group_image"]["name"])) {
        $fileName = basename($_FILES["group_image"]["name"]);
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $allowedTypes = ["jpg", "jpeg", "png", "gif"];

        if (in_array($fileExt, $allowedTypes)) {
            $newFileName = "group_" . time() . "." . $fileExt;
            $targetFilePath = $uploadDir . $newFileName;

            if (move_uploaded_file($_FILES["group_image"]["tmp_name"], $targetFilePath)) {
                $groupImage = $targetFilePath;
            } else {
                echo "<script>alert('❌ Image upload failed. Try again!');</script>";
            }
        } else {
            echo "<script>alert('⚠️ Only JPG, JPEG, PNG & GIF files are allowed.');</script>";
        }
    }

    // Insert data into database
    $query = "INSERT INTO tbl_groups (group_name, group_description, group_image, created_by) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $groupName, $groupDescription, $groupImage, $userId);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Group created successfully!'); window.location.href='groups.php';</script>";
    } else {
        echo "<script>alert('❌ Failed to create group.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Group</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #4facfe, #00f2fe);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            max-width: 400px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(15px);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
            text-align: center;
            color: white;
            animation: fadeIn 0.5s ease-in-out;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }

        label {
            display: block;
            font-weight: bold;
            font-size: 14px;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            background: rgba(255, 255, 255, 0.3);
            color: white;
            outline: none;
            transition: 0.3s;
        }

        input::placeholder, textarea::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        input:focus, textarea:focus {
            background: rgba(255, 255, 255, 0.5);
        }

        .file-input {
            display: none;
        }

        .upload-label {
            display: block;
            padding: 10px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }

        .upload-label:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        .preview-img {
            width: 100%;
            height: auto;
            max-height: 180px;
            border-radius: 8px;
            margin-top: 10px;
            display: none;
        }

        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-primary {
            background: linear-gradient(45deg, #ff416c, #ff4b2b);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(45deg, #ff4b2b, #ff416c);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>🚀 Create a New Group</h2>
    <form action="create_group.php" method="POST" enctype="multipart/form-data">
        <div class="input-group">
            <label>🏷️ Group Name:</label>
            <input type="text" name="group_name" placeholder="Enter group name" required>
        </div>

        <div class="input-group">
            <label>📝 Group Description:</label>
            <textarea name="group_description" placeholder="Describe your group..." rows="3" required></textarea>
        </div>

        <div class="input-group">
            <label for="groupImage" class="upload-label">📸 Upload Group Image</label>
            <input type="file" name="group_image" id="groupImage" class="file-input" accept="image/*">
            <img id="imagePreview" class="preview-img">
        </div>

        <button type="submit" class="btn btn-primary">✅ Create Group</button>
    </form>
</div>

<script>
document.getElementById("groupImage").addEventListener("change", function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewImg = document.getElementById("imagePreview");
            previewImg.src = e.target.result;
            previewImg.style.display = "block";
        };
        reader.readAsDataURL(file);
    }
});
</script>

</body>
</html>
