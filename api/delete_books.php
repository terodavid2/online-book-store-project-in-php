<?php
// 1. CORS Headers - Must match your Axios configuration
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// 2. Handle Preflight (OPTIONS) requests
// Browsers send this before the actual POST to check permissions
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include '../config/db.php';

try {
    // 3. Get JSON data from Axios request body
    // Axios sends data as a JSON string, not standard form-data
    $content = file_get_contents("php://input");
    $data = json_decode($content);

    // 4. Validate that an ID was provided
    if (!empty($data->id)) {
        // Sanitize the ID to prevent SQL injection
        $bookId = $conn->real_escape_string($data->id);

        // 5. Execute Delete Query
        $query = "DELETE FROM books WHERE id = '$bookId'";

        if ($conn->query($query)) {
            // Check if a row was actually removed
            if ($conn->affected_rows > 0) {
                http_response_code(200);
                echo json_encode([
                    "status" => "success",
                    "message" => "Book #$bookId was deleted successfully."
                ]);
            } else {
                // Query worked, but the ID didn't exist in the database
                http_response_code(404);
                echo json_encode([
                    "status" => "error",
                    "message" => "Book not found. No records were deleted."
                ]);
            }
        } else {
            throw new Exception($conn->error);
        }
    } else {
        // ID was missing from the React request
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Incomplete request. Book ID is required."
        ]);
    }

} catch (Exception $e) {
    // 6. Handle Database or System Errors
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Server Error: " . $e->getMessage()
    ]);
}

// 7. Cleanup
$conn->close();
?>