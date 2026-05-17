<?php 
include '../config/db.php'; 
session_start();

// Track which books the user has already bought to show the badge indicator
$purchased_book_ids = [];
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $check_purchase = "SELECT book_id FROM orders WHERE user_id = '$uid' AND (status = 'completed' OR status = 'success')";
    $purchase_res = $conn->query($check_purchase);
    if ($purchase_res) {
        while ($p_row = $purchase_res->fetch_assoc()) {
            $purchased_book_ids[] = $p_row['book_id'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Books | BookHeaven</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --success-green: #198754;
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        /* Glassmorphic Top Navigation Container bar styling */
        .navbar { 
            background: rgba(18, 18, 18, 0.6) !important; 
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08); 
        }
        .nav-link { transition: color 0.2s ease; }
        .nav-link:hover, .nav-link.active { color: var(--yellow-accent) !important; }

        /* Themed Navigation Action Control Buttons */
        .btn-yellow-solid { 
            background-color: var(--yellow-accent); 
            color: #121212 !important; 
            font-weight: 600;
            border-radius: 8px; 
            transition: all 0.3s ease; 
        }
        .btn-yellow-solid:hover { 
            background-color: var(--yellow-hover); 
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(241, 196, 15, 0.3);
        }
        
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

        /* Structural Content Form Grid */
        .book-grid { padding: 60px 0; }

        /* Transparent Glassmorphism Identity Book Module Cards */
        .book-card {
            background: rgba(255, 255, 255, 0.03) !important; 
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.07) !important; 
            border-radius: 16px;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            height: 100%;
            position: relative;
            text-decoration: none;
            color: #ffffff;
        }

        .book-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.07) !important;
            border-color: var(--yellow-accent) !important;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.5) !important;
        }

        .book-card img {
            height: 280px;
            width: 100%;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 15px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .book-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 5px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .book-author { color: rgba(255, 255, 255, 0.5); font-size: 0.9rem; margin-bottom: 12px; }
        .book-price { font-size: 1.2rem; font-weight: 700; color: var(--yellow-accent); }

        /* Purchased Badge styling hook */
        .purchase-badge {
            position: absolute;
            top: 25px;
            right: 25px;
            background-color: var(--success-green);
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: bold;
            text-transform: uppercase;
            box-shadow: 0 4px 12px rgba(0,0,0,0.6);
            z-index: 10;
            letter-spacing: 0.5px;
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
                    <li class="nav-item"><a class="nav-link mx-2 active" href="all-books.php">All Books</a></li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link mx-2" href="order_history.php">Orders</a></li>
                        <li class="nav-item"><a class="btn btn-yellow-outline px-4 ms-2" href="profile.php">Profile</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link mx-2" href="login.php">Login</a></li>
                        <li class="nav-item"><a class="btn btn-yellow-solid px-4 ms-2" href="signup.php">SignUp</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container book-grid">
        <h2 class="mb-5 fw-bold border-start border-3 border-warning ps-3">Explore Our Collection</h2>
        
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php
            $query = "SELECT * FROM books ORDER BY id DESC";
            $result = $conn->query($query);

            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $isPurchased = in_array($row['id'], $purchased_book_ids);
                    ?>
                    <div class="col">
                        <a href="book-details.php?id=<?php echo $row['id']; ?>" class="text-decoration-none">
                            <div class="card book-card p-3 shadow-sm">
                                
                                <?php if($isPurchased): ?>
                                    <div class="purchase-badge">
                                        <i class="fa-solid fa-circle-check me-1"></i> Purchased
                                    </div>
                                <?php endif; ?>

                                <img src="<?php echo htmlspecialchars($row['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($row['title']); ?>">
                                
                                <div class="card-body p-0 d-flex flex-column justify-content-between">
                                    <div>
                                        <h5 class="book-title text-truncate" title="<?php echo $row['title']; ?>"><?php echo htmlspecialchars($row['title']); ?></h5>
                                        <p class="book-author text-truncate">by <?php echo htmlspecialchars($row['author']); ?></p>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span class="book-price">₱ <?php echo number_format($row['price'], 2); ?></span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php
                }
            } else {
                echo "
                <div class='col-12 w-100'>
                    <div class='p-5 text-center rounded-3 w-100' style='background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.1)'>
                        <p class='text-muted mb-0'>No catalog records discovered in this active framework terminal.</p>
                    </div>
                </div>";
            }
            ?>
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