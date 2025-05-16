<?php
header('Content-Type: application/json');

$response = ["success" => false, "error" => "", "imagePath" => ""];

try {
    if (!empty($_FILES['image'])) {
        $image = $_FILES['image'];
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/pos/assets/images/repair-images/';
        $relativePath = 'assets/images/repair-images/';
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileExt = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExt, $allowedExtensions)) {
            throw new Exception("Invalid file type.");
        }

        $newFileName = uniqid('repair_', true) . '.' . $fileExt;
        $uploadPath = $uploadDir . $newFileName;

        if (move_uploaded_file($image['tmp_name'], $uploadPath)) {
            $response['success'] = true;
            $response['imagePath'] = $relativePath . $newFileName;
        } else {
            throw new Exception("move_uploaded_file failed. Path: $uploadPath");
        }
    } else {
        throw new Exception("No file uploaded.");
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
