<?php
session_start();
$conn = new mysqli("localhost", "root", "", "db_socialmedia");

$user_id = $_SESSION['user_id'] ?? 0;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $group_name = trim($_POST['group_name']);
    $group_description = trim($_POST['group_description']);
    $created_by = $user_id;

    $group_image = null;
    if (isset($_FILES['group_image']) && $_FILES['group_image']['error'] === 0) {
        $target_dir = "uploads/groups/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $file_name = time() . '_' . basename($_FILES["group_image"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["group_image"]["tmp_name"], $target_file)) {
            $group_image = $target_file;
        }
    }

    $stmt = $conn->prepare("INSERT INTO tbl_groups (group_name, group_description, group_image, created_by) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $group_name, $group_description, $group_image, $created_by);

    if ($stmt->execute()) {
        echo "<script>alert('Group added successfully!'); window.location.href='groups.php';</script>";
    } else {
        echo "<script>alert('Error adding group.');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Group</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to right, #6EE7B7, #3B82F6);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center font-sans">
    <div class="bg-white shadow-2xl rounded-2xl p-10 w-full max-w-lg animate-fade-in-down">
	
        <h2 class="text-3xl font-extrabold text-center text-gray-800 mb-6">✨ Create a New Group ✨</h2>
        <form action="add_group.php" method="POST" enctype="multipart/form-data" class="space-y-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Group Name</label>
                <input type="text" name="group_name" required class="w-full px-4 py-2 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition duration-150" placeholder="Enter group name">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Group Description</label>
                <textarea name="group_description" required rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition duration-150" placeholder="Describe the purpose of your group..."></textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Group Image</label>
                <input type="file" name="group_image" accept="image/*" class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:border-0 file:rounded-xl file:text-sm file:font-semibold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200 transition" />
            </div>

            <div class="text-center">
                <button type="submit" class="bg-gradient-to-r from-blue-500 to-green-400 text-white font-bold py-3 px-8 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:from-blue-600 hover:to-green-500">
                    🚀 Create Group
                </button>
				<div class="text-center">
    <a href="groups.php" class="inline-block mt-4 bg-white border border-blue-500 text-blue-600 font-semibold py-2 px-6 rounded-xl hover:bg-blue-50 transition duration-300">
        🔙 Go Back to Groups Page
    </a>
</div>

            </div>
        </form>
    </div>

    <script>
        // Fade-in animation
        document.querySelector('.animate-fade-in-down').style.opacity = 0;
        document.addEventListener("DOMContentLoaded", () => {
            setTimeout(() => {
                document.querySelector('.animate-fade-in-down').style.opacity = 1;
                document.querySelector('.animate-fade-in-down').style.transform = "translateY(0)";
            }, 100);
        });
    </script>
    <style>
        .animate-fade-in-down {
            opacity: 0;
            transform: translateY(-30px);
            transition: all 0.6s ease-out;
        }
    </style>
</body>
</html>
