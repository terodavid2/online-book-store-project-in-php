<?php
// 1. Enhanced CORS Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// 2. Handle preflight (OPTIONS) requests for Axios
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include '../../config/db.php'; // Path remains relative to your folder structure

try {
    // 3. Optimized SQL Query
    // Added table aliases (o, u, b) to make the query cleaner
    $query = "SELECT 
                o.id as order_id, 
                o.total_price, 
                o.status, 
                o.order_date,
                u.username as customer_name, 
                b.title as book_title,
                b.image_url
              FROM orders o
              JOIN users u ON o.user_id = u.id
              JOIN books b ON o.book_id = b.id
              ORDER BY o.order_date DESC";

    $result = $conn->query($query);
    $orders = [];

    if (!$result) {
        throw new Exception($conn->error);
    }

    // 4. Map data to Clean JSON structure
    while ($row = $result->fetch_assoc()) {
        $orders[] = [
            "id" => $row['order_id'],
            "customer" => $row['customer_name'],
            "book" => $row['book_title'],
            "image" => $row['image_url'],
            "amount" => (float)$row['total_price'],
            "status" => $row['status'],
            "date" => $row['order_date']
        ];
    }

    // 5. Success Response
    echo json_encode($orders);

} catch (Exception $e) {
    // 6. Error Response
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Database Query Failed: " . $e->getMessage()
    ]);
}

$conn->close();
?>