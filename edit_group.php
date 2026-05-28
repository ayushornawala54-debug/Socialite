<?php
session_start();
$conn = new mysqli("localhost", "root", "", "db_socialmedia");

// Redirect if user is not logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: form-login.php");
    exit();
}

$user_email = $_SESSION['user_email'];
$user_id = $_SESSION['user_id'];

// Validate group ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid request.");
}
$group_id = intval($_GET['id']);

// Fetch group details (only if the logged-in user created it)
$stmt = $conn->prepare("SELECT * FROM tbl_groups WHERE id = ? AND created_by = ?");
$stmt->bind_param("ii", $group_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("❌ You are not authorized to edit this group.");
}

$group = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $group_name = !empty($_POST["group_name"]) ? htmlspecialchars($_POST["group_name"]) : $group['group_name'];
    $group_description = !empty($_POST["description"]) ? htmlspecialchars($_POST["description"]) : $group['group_description'];

    // Handle new image upload
    if (!empty($_FILES["group_image"]["name"])) {
        $image_name = time() . "_" . basename($_FILES["group_image"]["name"]);
        $target_path = "uploads/" . $image_name;
        move_uploaded_file($_FILES["group_image"]["tmp_name"], $target_path);
    } else {
        $image_name = $group['group_image']; // Keep old image
    }

    // Update group details
    $stmt = $conn->prepare("UPDATE tbl_groups SET group_name = ?, group_description = ?, group_image = ? WHERE id = ? AND created_by = ?");
    $stmt->bind_param("sssii", $group_name, $group_description, $image_name, $group_id, $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Group updated successfully!'); window.location.href='groups.php';</script>";
    } else {
        echo "<script>alert('❌ Error updating group. Please try again.');</script>";
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
    <title>Edit Group</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            background: #fff;
            padding: 25px;
            width: 400px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        textarea {
            height: 80px;
            resize: vertical;
        }
        img {
            display: block;
            margin: 10px auto;
            border: 1px solid #ddd;
            border-radius: 5px;
            max-width: 150px;
        }
        input[type="file"] {
            margin-top: 5px;
        }
        button {
            width: 100%;
            margin-top: 15px;
            padding: 10px;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            background: #28a745;
            color: #fff;
            cursor: pointer;
        }
        button:hover {
            background: #218838;
        }
        .back-link {
            text-align: center;
            display: block;
            margin-top: 15px;
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Group</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="group_name">Group Name:</label>
        <input type="text" name="group_name" id="group_name" value="<?php echo htmlspecialchars($group['group_name']); ?>">

        <label for="description">Description:</label>
        <textarea name="description" id="description"><?php echo htmlspecialchars($group['group_description']); ?></textarea>

        <label>Current Image:</label>
        <img src="<?php echo !empty($group['group_image']) ? htmlspecialchars($group['group_image']) : "assets/default-group.jpg"; ?>" alt="Group Image">

        <label for="group_image">Upload New Image:</label>
        <input type="file" name="group_image" id="group_image">

        <button type="submit">Save Changes</button>
    </form>
    <a href="groups.php" class="back-link">⬅ Back to Groups</a>
</div>

</body>
</html>
