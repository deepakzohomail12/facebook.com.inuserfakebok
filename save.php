<?php
// upload.php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    // Set target directory for saving images
    $targetDir = 'images/'; // Make sure this directory is writable

    // Get file details
    $image = $_FILES['image'];
    $imageName = time() . '-' . basename($image['name']);
    $targetFile = $targetDir . $imageName;

    // Validate file type (only PNG in this case)
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    if ($imageFileType != 'png') {
        echo json_encode(['status' => 'error', 'message' => 'Only PNG images are allowed.']);
        exit;
    }

    // Move the uploaded file to the target directory
    if (move_uploaded_file($image['tmp_name'], $targetFile)) {
        echo json_encode(['status' => 'success', 'message' => 'Image uploaded successfully.', 'file' => $targetFile]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'There was an error uploading the image.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No image received.']);
}
?>
