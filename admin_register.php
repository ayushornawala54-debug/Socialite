<?php
session_start();
$conn = new mysqli("localhost", "root", "", "db_socialmedia");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = uniqid("admin_");
    $fullName = $_POST['fullName'];
    $emailId = $_POST['emailId'];
    $contactNumber = $_POST['contactNumber'];
    $bio = $_POST['bio'];
    $role = "admin";
    $address = $_POST['address'];
    $birthDay = $_POST['birthDay'];
    $password = $_POST['password']; // plain password (not hashed)

    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "upload/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $image = $targetDir . time() . '_' . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image);
    }

    $stmt = $conn->prepare("INSERT INTO tbl_admin (user_id, fullName, image, emailId, contactNumber, bio, role, address, birthDay, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $user_id, $fullName, $image, $emailId, $contactNumber, $bio, $role, $address, $birthDay, $password);

    if ($stmt->execute()) {
        header("Location: form-login.php?success=1");
        exit();
    } else {
        $message = "❌ Error: " . $stmt->error;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Registration</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to right, #667eea, #764ba2);
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 20px;
    }

    .form-container {
      background: white;
      padding: 30px;
      border-radius: 15px;
      max-width: 600px;
      width: 100%;
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }

    .form-group {
      margin-bottom: 15px;
    }

    label {
      font-weight: 500;
      margin-bottom: 5px;
      display: block;
      color: #444;
    }

    input[type="text"], input[type="email"], input[type="password"], input[type="date"], textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      transition: border-color 0.3s ease;
    }

    input:focus, textarea:focus {
      border-color: #667eea;
      outline: none;
    }

    .form-group input[type="file"] {
      border: none;
    }

    .image-preview {
      margin-top: 10px;
      max-height: 150px;
    }

    button {
      width: 100%;
      padding: 12px;
      background: #667eea;
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s;
    }

    button:hover {
      background: #5a67d8;
    }

    .message {
      margin-bottom: 15px;
      padding: 10px;
      background-color: #fef3c7;
      border-left: 5px solid #f59e0b;
      color: #92400e;
    }

    @media (max-width: 600px) {
      .form-container {
        padding: 20px;
      }
    }
  </style>
</head>
<body>

<div class="form-container">
  <h2>Admin Registration</h2>

  <?php if (!empty($message)): ?>
    <div class="message"><?= $message ?></div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <div class="form-group">
      <label>Full Name</label>
      <input type="text" name="fullName" required>
    </div>

    <div class="form-group">
      <label>Email ID</label>
      <input type="email" name="emailId" required>
    </div>

    <div class="form-group">
      <label>Contact Number</label>
      <input type="text" name="contactNumber" required>
    </div>

    <div class="form-group">
      <label>Bio</label>
      <textarea name="bio" rows="3" required></textarea>
    </div>

    <div class="form-group">
      <label>Address</label>
      <textarea name="address" rows="3" required></textarea>
    </div>

    <div class="form-group">
      <label>Birthday</label>
      <input type="date" name="birthDay" required>
    </div>

    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" required>
    </div>

    <div class="form-group">
      <label>Profile Image</label>
      <input type="file" name="image" accept="image/*" onchange="previewImage(event)" required>
      <img id="preview" class="image-preview" style="display:none;" />
    </div>

    <button type="submit">Register</button>
  </form>
</div>

<script>
  function previewImage(event) {
    const input = event.target;
    const reader = new FileReader();
    reader.onload = function(){
      const img = document.getElementById('preview');
      img.src = reader.result;
      img.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
  }
</script>

</body>
</html>
	
