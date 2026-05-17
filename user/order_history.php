<?php 
include '../config/db.php'; 
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. Fetch User Info for the Sidebar
$user_query = "SELECT username, email FROM users WHERE id = '$user_id'";
$user_res = $conn->query($user_query);
$user_data = ($user_res && $user_res->num_rows > 0) ? $user_res->fetch_assoc() : ['username' => 'Guest', 'email' => 'Not available'];

// 2. Fetch Order History 
$order_query = "SELECT 
                    orders.id as order_id, 
                    orders.total_price, 
                    orders.status, 
                    orders.order_date, 
                    books.title, 
                    books.image_url 
                FROM orders 
                JOIN books ON orders.book_id = books.id 
                WHERE orders.user_id = '$user_id' 
                ORDER BY orders.order_date DESC";

$order_result = $conn->query($order_query);

// 3. Error Handling
if (!$order_result) {
    $db_error = "Database Error: " . $conn->error;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History | BookHeaven</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --yellow-accent: #f1c40f;
            --yellow-hover: #f39c12;
        }

        body { 
            /* High-resolution dark library landscape overlay texture */
            background-image: linear-gradient(rgba(18, 18, 20, 0.93), rgba(12, 12, 14, 0.97)), url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?q=80&w=1920&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            color: white; 
            font-family: 'Segoe UI', sans-serif; 
            min-vh: 100vh;
        }
        
        /* Glassmorphic Sidebar Component Layout */
        .sidebar { 
            background: rgba(20, 20, 22, 0.55) !important; 
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            min-vh: 100vh; 
            padding: 40px 20px; 
            border-right: 1px solid rgba(255, 255, 255, 0.06); 
        }
        
        .profile-pic { 
            width: 75px; 
            height: 75px; 
            background: rgba(255, 255, 255, 0.05); 
            color: var(--yellow-accent); 
            border: 2px solid var(--yellow-accent);
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 2.2rem; 
            margin: 0 auto 15px; 
        }

        .nav-link { 
            color: rgba(255, 255, 255, 0.6); 
            padding: 12px 20px; 
            border-radius: 10px; 
            transition: all 0.2s ease; 
            text-decoration: none; 
            display: block; 
            margin-bottom: 6px; 
            font-weight: 500;
        }
        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.04);
            color: white;
        }
        .nav-link.active { 
            background-color: var(--yellow-accent); 
            color: #121214 !important; 
            font-weight: 600;
        }
        
        /* Main Content View Display Area */
        .main-content { padding: 50px; }
        
        /* Glassmorphic Order Tracking Row Card */
        .order-card { 
            background: rgba(255, 255, 255, 0.02) !important; 
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 16px; 
            padding: 24px; 
            margin-bottom: 20px; 
            display: flex; 
            align-items: center; 
            gap: 25px; 
            border: 1px solid rgba(255, 255, 255, 0.06); 
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); 
        }
        .order-card:hover { 
            border-color: var(--yellow-accent);
            background: rgba(255, 255, 255, 0.04) !important;
            transform: translateY(-2px);
        }

        .order-img { 
            width: 65px; 
            height: 95px; 
            object-fit: cover; 
            border-radius: 6px; 
            box-shadow: 0 8px 16px rgba(0,0,0,0.4); 
            border: 1px solid rgba(255,255,255,0.08);
        }
        
        .price-text {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--yellow-accent);
        }

        /* Customized Order System Badges */
        .status-badge { 
            padding: 6px 16px; 
            border-radius: 30px; 
            font-size: 0.72rem; 
            font-weight: 700; 
            text-transform: uppercase; 
            letter-spacing: 0.5px;
            display: inline-block;
        }
        .status-pending { background: rgba(241, 196, 15, 0.15); color: #f1c40f; border: 1px solid rgba(241, 196, 15, 0.3); }
        .status-completed { background: rgba(25, 135, 84, 0.15); color: #2ecc71; border: 1px solid rgba(25, 135, 84, 0.3); box-shadow: 0 0 15px rgba(46, 204, 113, 0.1); }
        .status-shipped { background: rgba(52, 152, 219, 0.15); color: #3498db; border: 1px solid rgba(52, 152, 219, 0.3); }

        .btn-yellow-outline {
            border: 2px solid var(--yellow-accent);
            color: var(--yellow-accent) !important;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-yellow-outline:hover {
            background-color: var(--yellow-accent);
            color: #121214 !important;
        }
        
        @media(max-width: 768px) {
            .sidebar { min-height: auto; padding: 20px; border-right: none; border-bottom: 1px solid rgba(255,255,255,0.06); }
            .main-content { padding: 25px; }
            .order-card { flex-direction: column; text-align: center; }
            .order-card .text-end { text-align: center !important; margin-top: 15px; }
        }
    </style>
</head>
<body>

<div class="container-fluid p-0">
    <div class="row g-0">
        <div class="col-md-3 col-lg-2 sidebar">
            <div class="text-center mb-4 pb-3 border-bottom border-secondary border-opacity-10">
                <div class="profile-pic"><i class="fa-solid fa-user"></i></div>
                <h5 class="mb-1 text-truncate fw-bold"><?php echo htmlspecialchars($user_data['username']); ?></h5>
                <p class="small text-white-50 text-truncate mb-0"><?php echo htmlspecialchars($user_data['email']); ?></p>
            </div>
            <div class="sidebar-nav">
                <a href="index.php" class="nav-link"><i class="fa-solid fa-house me-2"></i> Home</a>
                <a href="order_history.php" class="nav-link active"><i class="fa-solid fa-box me-2"></i> Order History</a>
                <a href="logout.php" class="nav-link text-danger mt-4"><i class="fa-solid fa-right-from-bracket me-2"></i> Log Out</a>
            </div>
        </div>

        <div class="col-md-9 col-lg-10 main-content">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-5">
                <h1 class="fw-bold m-0 border-start border-3 border-warning ps-3" style="color: #ffffff;">Order History</h1>
                <a href="all-books.php" class="btn btn-yellow-outline px-4 py-2 btn-sm">Continue Shopping</a>
            </div>

            <?php if (isset($db_error)): ?>
                <div class="alert alert-danger bg-danger bg-opacity-10 text-danger border-danger border-opacity-25 rounded-3">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo $db_error; ?>
                </div>
            <?php elseif ($order_result && $order_result->num_rows > 0): ?>
                
                <?php while($order = $order_result->fetch_assoc()): ?>
                    <div class="order-card shadow-sm">
                        <img src="<?php echo htmlspecialchars($order['image_url']); ?>" class="order-img" alt="Book Cover">
                        
                        <div class="flex-grow-1">
                            <h5 class="mb-2 fw-semibold"><?php echo htmlspecialchars($order['title']); ?></h5>
                            <p class="text-white-50 small mb-0">
                                <i class="fa-regular fa-calendar me-1 text-warning"></i> 
                                Ordered on: <?php echo date("M d, Y", strtotime($order['order_date'])); ?>
                            </p>
                            <p class="price-text mb-0 mt-2">₱ <?php echo number_format($order['total_price'], 2); ?></p>
                        </div>

                        <div class="text-end">
                            <?php 
                                $db_status = strtolower($order['status']);
                                $status_class = 'status-pending';
                                
                                if($db_status == 'completed' || $db_status == 'success') {
                                    $status_class = 'status-completed';
                                } elseif($db_status == 'shipped') {
                                    $status_class = 'status-shipped';
                                }
                            ?>
                            <span class="status-badge <?php echo $status_class; ?>">
                                <?php echo htmlspecialchars($order['status']); ?>
                            </span>
                            <p class="small text-white-50 mt-2 mb-0 font-monospace">ID: #ORD-<?php echo $order['order_id']; ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>

            <?php else: ?>
                <div class="text-center py-5 rounded-4" style="background: rgba(255,255,255,0.01); border: 1px dashed rgba(255,255,255,0.08)">
                    <i class="fa-solid fa-receipt fa-4x mb-3 text-secondary" style="opacity: 0.2;"></i>
                    <h4 class="text-white-50">No orders found</h4>
                    <p class="text-muted small">Looks like you haven't bought any books yet.</p>
                    <a href="all-books.php" class="btn btn-yellow-outline px-4 mt-2 rounded-pill">Browse Books</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>