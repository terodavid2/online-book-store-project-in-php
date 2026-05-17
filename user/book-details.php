<?php
include '../config/db.php';
session_start();

// Get the book ID safely
$book_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Fetch book details
$query = "SELECT * FROM books WHERE id = $book_id";
$result = mysqli_query($conn, $query);

// Check if book exists
if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: all-books.php");
    exit();
}

$book = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book['title']); ?> | BookHeaven</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --yellow-accent: #f1c40f;
            --yellow-hover: #f39c12;
        }

        body {
            /* High-resolution online dark library backdrop texture overlay */
            background-image: linear-gradient(rgba(18, 18, 20, 0.94), rgba(12, 12, 14, 0.98)), url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?q=80&w=1920&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            color: white;
            font-family: 'Segoe UI', sans-serif;
            min-vh: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Glassmorphic Navbar Configuration */
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

        .details-container {
            padding: 60px 0;
            flex: 1;
        }

        /* Glassmorphic Image Backdrop Panel Holder */
        .book-img-holder {
            background: rgba(255, 255, 255, 0.02) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            position: relative;
            box-shadow: 0 15px 35px rgba(0,0,0,0.4);
        }

        .book-img-holder img {
            max-width: 100%;
            height: 480px;
            object-fit: contain;
            border-radius: 8px;
            filter: drop-shadow(0 15px 25px rgba(0,0,0,0.6));
        }

        .action-overlay {
            position: absolute;
            top: 25px;
            right: 25px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* Action Circle Floating Controllers */
        .icon-btn {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        .btn-fav {
            background: rgba(255, 255, 255, 0.9);
            color: #ff4757;
        }
        .btn-fav:hover {
            background: #ff4757;
            color: white;
        }

        .btn-cart {
            background: var(--yellow-accent);
            color: #121214;
        }
        .btn-cart:hover {
            background: var(--yellow-hover);
        }

        .icon-btn:hover {
            transform: scale(1.1) translateY(-2px);
        }

        .book-title {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .book-author {
            color: rgba(255, 255, 255, 0.5);
            font-size: 1.2rem;
            margin-bottom: 25px;
        }

        .book-desc {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.8;
            margin-bottom: 35px;
            font-size: 1.05rem;
        }

        .book-lang {
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.95rem;
        }

        .book-price-large {
            font-size: 2.4rem;
            font-weight: 700;
            color: var(--yellow-accent);
            border-top: 1px solid rgba(255,255,255,0.08);
            padding-top: 20px;
        }

        .back-btn {
            margin-bottom: 30px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .back-btn:hover {
            background-color: white;
            color: #121214 !important;
        }

        .custom-text-yellow { color: var(--yellow-accent) !important; }
        .footer-link { transition: color 0.2s ease; }
        .footer-link:hover { color: var(--yellow-accent) !important; }

        @media(max-width: 768px) {
            .book-img-holder img {
                height: 340px;
            }
            .book-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark px-4 py-3 sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3" href="index.php">BookHeaven</a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link mx-2" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link mx-2" href="all-books.php">All Books</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="btn btn-yellow-outline px-4 ms-2" href="profile.php">Profile</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link mx-2" href="login.php">Login</a></li>
                    <li class="nav-item">
                        <a class="btn btn-yellow-solid px-4 ms-2" href="signup.php">Sign Up</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container details-container">

    <a href="all-books.php" class="btn btn-outline-light back-btn px-4">
        <i class="fa-solid fa-arrow-left me-2"></i> Back to Books
    </a>

    <div class="row g-5">
        <div class="col-md-5">
            <div class="book-img-holder">
                <img src="<?php echo htmlspecialchars($book['image_url']); ?>" alt="Book Cover">

                <div class="action-overlay">
                    <form action="add_to_favorites.php" method="POST">
                        <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                        <button type="submit" class="icon-btn btn-fav" title="Add to Favorites">
                            <i class="fa-solid fa-heart"></i>
                        </button>
                    </form>

                    <form action="add_to_cart.php" method="POST">
                        <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                        <button type="submit" class="icon-btn btn-cart" title="Add to Cart">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7 d-flex flex-column justify-content-center">
            <h1 class="book-title border-start border-4 border-warning ps-3">
                <?php echo htmlspecialchars($book['title']); ?>
            </h1>

            <p class="book-author">
                by <?php echo htmlspecialchars($book['author']); ?>
            </p>

            <p class="book-desc">
                <?php echo nl2br(htmlspecialchars($book['description'])); ?>
            </p>

            <div class="book-lang">
                <i class="fa-solid fa-earth-americas custom-text-yellow"></i>
                English Language Edition
            </div>

            <div class="book-price-large">
                ₱ <?php echo number_format($book['price'], 2); ?>
            </div>
        </div>
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