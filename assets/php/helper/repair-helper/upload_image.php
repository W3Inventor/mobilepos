<?php
header('Content-Type: application/json');

$response = ["success" => false, "error" => "", "imagePath" => ""];

try {
    if (!empty($_FILES['image']) && isset($_FILES['image']['tmp_name'])) {
        $image = $_FILES['image'];

        // Define upload directory using a safe absolute path
        $uploadDir = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'repair-images' . DIRECTORY_SEPARATOR;
        $relativePath = 'assets/images/repair-images/';  // relative path for use in HTML (src attribute)

        // Create the directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new Exception("Failed to create upload directory.");
            }
        }

        // Validate file extension
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExt = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExt, $allowedExtensions)) {
            throw new Exception("Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.");
        }

        // Sanitize and generate a unique filename for the upload
        $newFileName = uniqid('repair_', true) . '.' . $fileExt;
        // Remove any additional dots from uniqid to avoid multiple dots in filename
        $newFileName = str_replace('.', '_', $newFileName) . '.' . $fileExt;  // ensure only one dot before extension
        $uploadPath = $uploadDir . $newFileName;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($image['tmp_name'], $uploadPath)) {
            $response['success']  = true;
            $response['imagePath'] = $relativePath . $newFileName;  // save relative path to return for frontend usage
        } else {
            throw new Exception("Failed to save the uploaded file on the server. Please try again.");
        }
    } else {
        throw new Exception("No file was uploaded.");
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
