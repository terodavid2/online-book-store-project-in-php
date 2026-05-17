<?php 
include '../config/db.php'; 
session_start();

// Redirect to login if session is not set
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch User Info for the Sidebar
$user_query = "SELECT username, email FROM users WHERE id = '$user_id'";
$user_res = $conn->query($user_query);
$user_data = ($user_res && $user_res->num_rows > 0) ? $user_res->fetch_assoc() : ['username' => 'Guest', 'email' => 'Not available'];

// Handle "Remove from favourites" logic safely
if (isset($_POST['remove_fav'])) {
    $fav_id = mysqli_real_escape_string($conn, $_POST['fav_id']);
    $conn->query("DELETE FROM favourites WHERE id = '$fav_id' AND user_id = '$user_id'");
    header("Location: profile.php");
    exit();
}

// Fetch Favourite Books
$fav_query = "SELECT favourites.id as fav_id, books.* FROM favourites 
              JOIN books ON favourites.book_id = books.id 
              WHERE favourites.user_id = '$user_id'";
$fav_result = $conn->query($fav_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | BookHeaven</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --yellow-accent: #f1c40f;
            --yellow-hover: #f39c12;
            --card-remove-bg: rgba(231, 76, 60, 0.15);
            --card-remove-hover: rgba(231, 76, 60, 0.3);
        }

        body { 
            /* Synchronized high-resolution online dark library landscape texture */
            background-image: linear-gradient(rgba(18, 18, 20, 0.93), rgba(12, 12, 14, 0.97)), url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?q=80&w=1920&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            color: #ffffff; 
            font-family: 'Segoe UI', sans-serif; 
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Header Component Guard */
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
        
        /* Glassmorphic Sidebar Component Layout */
        .sidebar {
            background: rgba(20, 20, 22, 0.55) !important; 
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            min-height: calc(100vh - 82px);
            padding: 40px 20px;
            border-right: 1px solid rgba(255, 255, 255, 0.06);
            display: flex;
            flex-direction: column;
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
        
        .sidebar-nav .nav-link {
            color: rgba(255, 255, 255, 0.6);
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 6px;
            transition: all 0.2s ease;
            font-weight: 500;
        }
        .sidebar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.04);
            color: #ffffff;
        }
        .sidebar-nav .nav-link.active {
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--yellow-accent);
            border-left: 3px solid var(--yellow-accent);
            border-radius: 0 10px 10px 0;
            padding-left: 17px;
        }
        .btn-logout {
            margin-top: auto;
            color: #ff4d4d !important;
            border-top: 1px solid rgba(255,255,255,0.06);
            padding-top: 20px;
            border-radius: 0 !important;
        }
        .btn-logout:hover {
            background: none !important;
            color: #ff1a1a !important;
        }

        /* Main Workspace Display Content Block */
        .main-content { padding: 50px; flex: 1; }
        .section-title { font-size: 2.5rem; font-weight: 700; margin-bottom: 40px; }
        
        /* Glassmorphic Favorite Resource Presentation Card */
        .fav-card {
            background: rgba(255, 255, 255, 0.02) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            padding: 20px;
            border-radius: 16px;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        .fav-card:hover {
            border-color: var(--yellow-accent);
            background: rgba(255, 255, 255, 0.04) !important;
            transform: translateY(-4px);
        }
        .fav-card img {
            width: 100%;
            height: 260px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 18px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.05);
        }
        .card-price {
            font-size: 1.2rem;
            color: var(--yellow-accent);
            font-weight: 700;
        }
        
        /* Destructive System Actions Call Styling */
        .btn-remove {
            background-color: var(--card-remove-bg);
            color: #ff6b6b;
            border: 1px solid rgba(231, 76, 60, 0.2);
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            font-size: 0.88rem;
            font-weight: 600;
            margin-top: auto;
            transition: all 0.2s ease;
        }
        .btn-remove:hover { 
            background-color: var(--card-remove-hover);
            color: #ff4d4d;
        }

        .custom-text-yellow { color: var(--yellow-accent) !important; }
        .footer-link { transition: color 0.2s ease; }
        .footer-link:hover { color: var(--yellow-accent) !important; }

        @media(max-width: 768px) {
            .sidebar { min-height: auto; padding: 20px; border-right: none; border-bottom: 1px solid rgba(255,255,255,0.06); }
            .main-content { padding: 25px; }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark px-4 py-3 sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold fs-3" href="index.php">BookHeaven</a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link mx-2" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link mx-2" href="all-books.php">All Books</a></li>
                    <li class="nav-item"><a class="nav-link mx-2" href="cart.php">Cart</a></li>
                    <li class="nav-item"><a class="btn btn-yellow-outline px-4 ms-2 active" href="profile.php">Profile</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="text-center mb-4 pb-2">
                    <div class="profile-pic">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <h5 class="mb-1 text-truncate fw-bold"><?php echo htmlspecialchars($user_data['username']); ?></h5>
                    <p class="small text-white-50 text-truncate mb-0"><?php echo htmlspecialchars($user_data['email']); ?></p>
                </div>

                <div class="sidebar-nav d-flex flex-column h-100">
                   
                    <a href="order_history.php" class="nav-link"><i class="fa-solid fa-box me-2"></i> Order History</a>
                
                    
                    <a href="logout.php" class="nav-link btn-logout mt-4">
                        <i class="fa-solid fa-right-from-bracket me-2"></i> Log Out
                    </a>
                </div>
            </div>

            <div class="col-md-9 col-lg-10 main-content">
                <h1 class="section-title border-start border-3 border-warning ps-3">Favorite Books</h1>

                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
                    <?php if ($fav_result && $fav_result->num_rows > 0): ?>
                        <?php while($book = $fav_result->fetch_assoc()): ?>
                            <div class="col">
                                <div class="fav-card shadow-sm">
                                    <img src="<?php echo htmlspecialchars($book['image_url']); ?>" alt="Book Cover">
                                    <h6 class="fw-bold mb-1 text-truncate" title="<?php echo htmlspecialchars($book['title']); ?>"><?php echo htmlspecialchars($book['title']); ?></h6>
                                    <p class="text-white-50 small mb-2 text-truncate">by <?php echo htmlspecialchars($book['author']); ?></p>
                                    
                                    <p class="card-price mb-3">₱ <?php echo number_format($book['price'], 2); ?></p>
                                    
                                    <form method="POST" onsubmit="return confirm('Are you sure you want to remove this book from your favorites?');">
                                        <input type="hidden" name="fav_id" value="<?php echo $book['fav_id']; ?>">
                                        <button type="submit" name="remove_fav" class="btn-remove">
                                            <i class="fa-solid fa-trash-can me-2"></i>Remove from favourites
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                      
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <footer class="mt-auto py-4 border-top border-secondary border-opacity-10" style="background: rgba(10, 10, 12, 0.4); backdrop-filter: blur(10px);">
        <div class="container-fluid px-5 text-center text-md-between d-md-flex align-items-center justify-content-between">
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