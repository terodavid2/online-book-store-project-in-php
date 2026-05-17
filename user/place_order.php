<?php
include '../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. Fetch all items currently in this user's cart
$cart_query = "SELECT * FROM cart WHERE user_id = '$user_id'";
$cart_result = $conn->query($cart_query);

if ($cart_result->num_rows > 0) {
    while ($row = $cart_result->fetch_assoc()) {
        $book_id = $row['book_id'];
        
        // 2. Insert each item into the orders table
        // We fetch the price from the books table to ensure accuracy
        $book_info = $conn->query("SELECT price FROM books WHERE id = '$book_id'")->fetch_assoc();
        $price = $book_info['price'];

        $insert_order = "INSERT INTO orders (user_id, book_id, total_price, status) 
                        VALUES ('$user_id', '$book_id', '$price', 'Pending')";
        $conn->query($insert_order);
    }

    // 3. Clear the cart after placing the order
    $conn->query("DELETE FROM cart WHERE user_id = '$user_id'");

    header("Location: profile.php?success=Order placed successfully!");
} else {
    header("Location: cart.php?error=Cart is empty");
}
exit();
?>