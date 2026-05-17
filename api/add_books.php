<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include '../config/db.php';

try {
    // IMPORTANT: For FormData (files), we use $_POST, NOT file_get_contents
    if (!isset($_POST['title']) || !isset($_FILES['image'])) {
        throw new Exception("Missing required fields. Title and Image are mandatory.");
    }

    $title = $conn->real_escape_string($_POST['title']);
    $author = $conn->real_escape_string($_POST['author'] ?? 'Unknown');
    $price = $conn->real_escape_string($_POST['price'] ?? 0);
    $category = $conn->real_escape_string($_POST['category'] ?? 'General');
    $description = $conn->real_escape_string($_POST['description'] ?? '');

    // Handle File Upload
    $upload_dir = "uploads/";
    
    // Create directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file = $_FILES['image'];
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($file_ext, $allowed)) {
        throw new Exception("Invalid file type. Only JPG, PNG, and WebP are allowed.");
    }

    // Create a clean, unique filename
    $new_file_name = time() . "_" . uniqid() . "." . $file_ext;
    $target_path = $upload_dir . $new_file_name;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        // Save the FULL URL to the database so React can display it easily
        $image_url = "http://localhost/book-shop-system/api/" . $target_path;

        $sql = "INSERT INTO books (title, author, price, category, description, image_url) 
                VALUES ('$title', '$author', '$price', '$category', '$description', '$image_url')";

        if ($conn->query($sql)) {
            echo json_encode(["status" => "success", "message" => "Book uploaded successfully!"]);
        } else {
            throw new Exception("Database Error: " . $conn->error);
        }
    } else {
        throw new Exception("Failed to save the image file to the server folder.");
    }

} catch (Exception $e) {
    http_response_code(400); // Send a 400 error so React goes to the catch block
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

$conn->close();
?>