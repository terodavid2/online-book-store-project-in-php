<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'OPTIONS') { exit; } // Handle Preflight

switch($method) {
    case 'GET':
        if(isset($_GET['customers'])) {
            $sql = "SELECT id, username, email FROM users WHERE role='user'";
        } else {
            $sql = "SELECT * FROM books ORDER BY id DESC";
        }
        $result = $conn->query($sql);
        echo json_encode($result->fetch_all(MYSQLI_ASSOC));
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $conn->prepare("INSERT INTO books (title, author, price, description, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdss", $data['title'], $data['author'], $data['price'], $data['description'], $data['image_url']);
        if($stmt->execute()) echo json_encode(["message" => "Success"]);
        break;

    case 'DELETE':
        $id = $_GET['id'];
        $conn->query("DELETE FROM books WHERE id=$id");
        echo json_encode(["message" => "Deleted"]);
        break;
}
?>