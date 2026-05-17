<?php 
include '../config/db.php'; 
session_start();

// Mock User ID for demonstration (Replace with actual session id after login logic)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; 
}
$user_id = $_SESSION['user_id'];

// Handle Item Removal
if (isset($_GET['remove'])) {
    $item_id = mysqli_real_escape_string($conn, $_GET['remove']);
    $conn->query("DELETE FROM cart WHERE id = '$item_id' AND user_id = '$user_id'");
    header("Location: cart.php");
    exit();
}

// Fetch Cart Items
$query = "SELECT cart.id as cart_id, books.* FROM cart 
          JOIN books ON cart.book_id = books.id 
          WHERE cart.user_id = '$user_id'";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart | BookHeaven</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --yellow-accent: #f1c40f;
            --yellow-hover: #f39c12;
        }

        body { 
            /* Synchronized high-resolution online library background landscape texture */
            background-image: linear-gradient(rgba(18, 18, 20, 0.92), rgba(12, 12, 14, 0.97)), url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?q=80&w=1920&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            color: #ffffff; 
            font-family: 'Segoe UI', sans-serif; 
            overflow-x: hidden;
            min-vh: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Glassmorphic Top Navigation Container */
        .navbar { 
            background: rgba(18, 18, 18, 0.6) !important; 
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08); 
        }
        .nav-link { transition: color 0.2s ease; }
        .nav-link:hover, .nav-link.active { color: var(--yellow-accent) !important; }
        
        .btn-yellow-outline {
            border: 2px solid var(--yellow-accent);
            color: var(--yellow-accent) !important;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-yellow-outline:hover {
            background-color: var(--yellow-accent);
            color: #121214 !important;
        }

        .cart-container { padding: 60px 0; flex: 1; }
        .cart-title { font-size: 2.5rem; font-weight: 700; margin-bottom: 40px; }
        
        /* Glassmorphic Item Row Wrapper Card */
        .cart-item {
            background: rgba(255, 255, 255, 0.03) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.07);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: background 0.3s ease;
        }
        .cart-item:hover {
            background: rgba(255, 255, 255, 0.06) !important;
        }

        .item-info { display: flex; align-items: center; gap: 20px; }
        .item-img { width: 60px; height: 85px; object-fit: cover; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); }
        .item-details h5 { margin: 0; font-weight: 600; }
        .item-details p { margin: 0; color: rgba(255,255,255,0.5); font-size: 0.9rem; margin-top: 4px; }
        
        .item-price { font-size: 1.4rem; font-weight: 700; color: var(--yellow-accent); }
        
        .remove-btn {
            background: #ff4d4d;
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .remove-btn:hover { background: #ff1a1a; transform: scale(1.05); }

        /* Transparent Summary Glass Box Panel Component */
        .summary-box {
            background: rgba(255, 255, 255, 0.03) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.07);
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }
        .summary-total { font-size: 1.8rem; font-weight: 700; color: var(--yellow-accent); }
        
        /* Yellow Order Placement Action Call */
        .btn-place { 
            background: var(--yellow-accent); 
            color: #121214; 
            font-weight: 700; 
            border: none; 
            padding: 14px; 
            transition: all 0.3s ease;
        }
        .btn-place:hover { 
            background: var(--yellow-hover);
            box-shadow: 0 4px 15px rgba(241, 196, 15, 0.2);
        }

        .custom-text-yellow { color: var(--yellow-accent) !important; }
        .footer-link { transition: color 0.2s ease; }
        .footer-link:hover { color: var(--yellow-accent) !important; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark px-4 py-3 sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="index.php">BookHeaven</a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link mx-2" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link mx-2" href="all-books.php">All Books</a></li>
                    <li class="nav-item"><a class="nav-link mx-2 active" href="cart.php">Cart</a></li>
                    <li class="nav-item"><a class="btn btn-yellow-outline px-4 ms-2" href="profile.php">Profile</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container cart-container">
        <h1 class="cart-title border-start border-3 border-warning ps-3">Your Shopping Cart</h1>
        
        <div class="row g-4">
            <div class="col-md-8">
                <?php 
                $total_price = 0;
                $count = 0;
                if ($result && $result->num_rows > 0): 
                    while($item = $result->fetch_assoc()): 
                        $total_price += $item['price'];
                        $count++;
                ?>
                <div class="cart-item shadow-sm">
                    <div class="item-info">
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" class="item-img" alt="Book Cover">
                        <div class="item-details">
                            <h5><?php echo htmlspecialchars($item['title']); ?></h5>
                            <p><?php echo htmlspecialchars(substr($item['description'], 0, 80)); ?>...</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-4">
                        <div class="item-price">₱ <?php echo number_format($item['price'], 2); ?></div>
                        <a href="cart.php?remove=<?php echo $item['cart_id']; ?>" class="remove-btn d-flex align-items-center justify-content-center" title="Remove Item">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </div>
                </div>
                <?php endwhile; else: ?>
                    <div class="text-center py-5 rounded-3" style="background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.1)">
                        <i class="fa-solid fa-cart-shopping fa-3x mb-3 text-secondary"></i>
                        <h4>Your cart is empty</h4>
                        <a href="all-books.php" class="btn btn-yellow-outline mt-3 rounded-pill px-4">Browse Books</a>
                    </div>
                <?php endif; ?>
            </div>

            <?php if($count > 0): ?>
            <div class="col-md-4">
                <div class="summary-box">
                    <h3 class="mb-4 fw-bold">Total Amount</h3>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-white-50"><?php echo $count; ?> Summary Items:</span>
                        <span class="summary-total">₱ <?php echo number_format($total_price, 2); ?></span>
                    </div>
                    <hr class="text-secondary opacity-25">
                    <form action="place_order.php" method="POST">
                        <input type="hidden" name="total_amount" value="<?php echo $total_price; ?>">
                        <button type="submit" class="btn btn-place w-100 mt-2 rounded-3">Place your order</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <footer class="mt-auto py-4 border-top border-secondary border-opacity-10" style="background: rgba(10, 10, 12, 0.4); backdrop-filter: blur(10px);">
        <div class="container text-center text-md-between d-md-flex align-items-center justify-content-between">
            <p class="text-white-50 mb-2 mb-md-0 small">
                &copy; <?= date('Y') ?> <span class="custom-text-yellow fw-semibold">BookHeaven</span> Digital Ecosystem Framework.
            </p>
            <div class="small">
                <a href="index.php" class="text-white-50 text-decoration-none mx-2 footer-link">Home</a>
                <span class="text-secondary">|</span>
                <a href="all-books.php" class="text-white-50 text-decoration-none mx-2 footer-link">Catalog</a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>