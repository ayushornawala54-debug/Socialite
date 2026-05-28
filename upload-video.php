<?php
$conn = new mysqli("localhost", "root", "", "db_socialmedia");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $uploader = $_POST['uploader'];
    
    $thumbnail = "uploads/" . basename($_FILES["thumbnail"]["name"]);
    move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $thumbnail);
    
    $video = "uploads/" . basename($_FILES["video"]["name"]);
    move_uploaded_file($_FILES["video"]["tmp_name"], $video);

    $stmt = $conn->prepare("INSERT INTO videos (title, uploader, thumbnail, video_url) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $uploader, $thumbnail, $video);
    
    if ($stmt->execute()) {
        echo "Video uploaded successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<html>
<head>

</head>
<body>
<form id="uploadForm" enctype="multipart/form-data" method="POST">
    <input type="text" name="title" placeholder="Video Title" required>
    <input type="text" name="uploader" placeholder="Uploader Name" required>
    <input type="file" name="thumbnail" accept="image/*" required>
    <input type="file" name="video" accept="video/*" required>
    <button type="submit">Upload Video</button>
</form>

<div id="uploadStatus"></div>

<script>
document.getElementById("uploadForm").addEventListener("submit", function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    
    fetch("upload-video.php", {
        method: "POST",
        body: formData
    }).then(response => response.text())
      .then(data => {
          document.getElementById("uploadStatus").innerHTML = data;
          loadVideos(); // Refresh video list after upload
      });
});
</script>
</body>
</html>