<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include '../config/db.php';

try {
    /**
     * SQL EXPLAINER:
     * 1. We select user details.
     * 2. LEFT JOIN orders to see what they bought (if anything).
     * 3. LEFT JOIN books to get the actual titles.
     * 4. GROUP_CONCAT merges multiple book titles into one string separated by commas.
     * 5. GROUP BY u.id ensures we get one row per customer.
     */
    $sql = "SELECT 
                u.id, 
                u.username, 
                u.email, 
                u.created_at,
                GROUP_CONCAT(b.title SEPARATOR ', ') AS purchased_books
            FROM users u
            LEFT JOIN orders o ON u.id = o.user_id
            LEFT JOIN books b ON o.book_id = b.id
            GROUP BY u.id
            ORDER BY u.id DESC";

    $result = $conn->query($sql);

    if ($result) {
        $customers = [];
        while ($row = $result->fetch_assoc()) {
            // If the user hasn't bought anything, GROUP_CONCAT returns NULL.
            // We ensure it returns an empty string or 'None' for React.
            $row['purchased_books'] = $row['purchased_books'] ?? "";
            $customers[] = $row;
        }
        
        echo json_encode($customers);
    } else {
        throw new Exception("Query Failed: " . $conn->error);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}

$conn->close();
?>