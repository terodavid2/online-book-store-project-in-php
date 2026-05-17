<?php
require_once '../config/db.php';
session_start();

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page with a message
    header("Location: login.php?msg=Please login to add items to your cart");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $book_id = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;

    if ($book_id > 0) {
        // 2. Check if the book is already in the cart to avoid duplicates
        $check_query = "SELECT id FROM cart WHERE user_id = ? AND book_id = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("ii", $user_id, $book_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Book already exists in cart, you could increment quantity here
            // For now, we'll just redirect to the cart
            header("Location: cart.php?info=Item is already in your cart");
        } else {
            // 3. Insert the book into the cart table
            $insert_query = "INSERT INTO cart (user_id, book_id, quantity) VALUES (?, ?, 1)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("ii", $user_id, $book_id);

            if ($stmt->execute()) {
                // Success: Redirect to cart page
                header("Location: cart.php?success=Book added to cart");
            } else {
                // Error: Redirect back to details
                header("Location: book-details.php?id=$book_id&error=Could not add to cart");
            }
        }
        $stmt->close();
    } else {
        header("Location: all-books.php");
    }
} else {
    // If accessed directly without POST, go back to catalog
    header("Location: all-books.php");
}

$conn->close();
?>