<?php
// 1. Enhanced CORS Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// 2. Handle preflight (OPTIONS) requests
// This tells the browser "Yes, I allow these methods" before the actual GET request happens
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include '../config/db.php';

try {
    // 3. Fetch Books
    $query = "SELECT * FROM books ORDER BY id DESC";
    $result = $conn->query($query);

    if (!$result) {
        throw new Exception($conn->error);
    }

    $books = [];
    while($row = $result->fetch_assoc()) {
        // Ensure numeric values are sent correctly (optional but helpful)
        if(isset($row['price'])) $row['price'] = (float)$row['price'];
        if(isset($row['stock'])) $row['stock'] = (int)$row['stock'];
        
        $books[] = $row;
    }

    // 4. Send Success Response
    echo json_encode($books);

} catch (Exception $e) {
    // 5. Handle Errors Gracefully
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}

$conn->close();
?>