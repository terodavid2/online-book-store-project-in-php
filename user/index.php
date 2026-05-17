<?php include '../config/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookHeaven | Discover Your Next Read</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { 
            /* High-resolution online dark library backdrop texture overlay */
            background-image: linear-gradient(rgba(18, 18, 20, 0.92), rgba(12, 12, 14, 0.97)), url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?q=80&w=1920&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            color: #fff; 
            font-family: 'Segoe UI', sans-serif; 
            overflow-x: hidden; 
        }
        
        /* Glassmorphic Navbar Configuration */
        .navbar { 
            background: rgba(18, 18, 18, 0.6) !important; 
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08); 
        }
        .nav-link { transition: color 0.2s ease; }
        .nav-link:hover, .nav-link.active { color: #f1c40f !important; }
        
        /* Hero Framework */
        .hero { padding: 120px 0 80px 0; }
        .hero-title { line-height: 1.2; }
        
        /* Transparent Brand Yellow Themed Buttons */
        .btn-yellow-solid { 
            background-color: #f1c40f; 
            color: #121212 !important; 
            font-weight: 600;
            border-radius: 8px; 
            transition: all 0.3s ease; 
        }
        .btn-yellow-solid:hover { 
            background-color: #f39c12; 
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(241, 196, 15, 0.3);
        }
        
        .btn-yellow-outline {
            border: 2px solid #f1c40f;
            color: #f1c40f !important;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-yellow-outline:hover {
            background-color: #f1c40f;
            color: #121214 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(241, 196, 15, 0.2);
        }
        
        /* Transparent Glassmorphism Book Cards */
        .book-card { 
            background: rgba(255, 255, 255, 0.03) !important; 
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.07) !important; 
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); 
            color: white; 
            border-radius: 16px; 
            text-decoration: none;
        }
        .book-card:hover { 
            transform: translateY(-10px); 
            background: rgba(255, 255, 255, 0.07) !important; 
            border-color: #f1c40f !important;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.5) !important;
        }
        .price-tag { color: #f1c40f; font-weight: 700; font-size: 1.15rem; }

        /* Custom Global Utilities */
        .custom-text-yellow { color: #f1c40f !important; }
        .footer-link { transition: all 0.2s ease; color: rgba(255, 255, 255, 0.5) !important; }
        .footer-link:hover { color: #f1c40f !important; padding-left: 4px; }
        .social-icon {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: rgba(255, 255, 255, 0.6);
            border-radius: 50%;
            transition: all 0.2s ease;
        }
        .social-icon:hover {
            background: #f1c40f;
            color: #121214;
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-dark px-4 py-3 sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="index.php">📚 BookHeaven</a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                    <li class="nav-item"><a class="nav-link mx-2 active" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link mx-2" href="all-books.php">All Books</a></li>
                    <li class="nav-item"><a class="nav-link mx-2" href="login.php">LogIn</a></li>
                    <li class="nav-item"><a class="btn btn-yellow-solid px-4 ms-2" href="signup.php">SignUp</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5 py-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold border-start border-3 border-warning ps-3">Recently Added Books</h3>
            <a href="all-books.php" class="text-decoration-none custom-text-yellow small fw-semibold">
                View All <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>
        
        <div class="row g-4">
            <?php
            $res = $conn->query("SELECT * FROM books ORDER BY id DESC LIMIT 4");
            if($res && $res->num_rows > 0):
                while($row = $res->fetch_assoc()): ?>
                <div class="col-6 col-md-3">
                    <a href="book-details.php?id=<?= $row['id'] ?>" class="card book-card p-3 h-100 shadow-sm">
                        <img src="<?= htmlspecialchars($row['image_url']) ?>" class="card-img-top rounded mb-3" alt="<?= htmlspecialchars($row['title']) ?>" style="height: 270px; object-fit: cover; border: 1px solid rgba(255,255,255,0.05);">
                        <div class="card-body p-0 d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="fw-bold mb-1 text-truncate" title="<?= $row['title'] ?>"><?= $row['title'] ?></h6>
                                <p class="text-white-50 small mb-2 text-truncate">by <?= $row['author'] ?></p>
                            </div>
                            <p class="price-tag mb-0">₱ <?= number_format($row['price'], 2) ?></p>
                        </div>
                    </a>
                </div>
                <?php endwhile; 
            else: ?>
                <div class="col-12">
                    <div class="p-5 text-center rounded-3" style="background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.1)">
                        <p class="text-muted mb-0">No books found in the library database terminal.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer class="mt-auto border-top border-secondary border-opacity-10" style="background: rgba(10, 10, 12, 0.85); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px);">
        <div class="container pt-5 pb-4">
            <div class="row g-4 mb-4">
             
                    <p class="text-white-50 small pe-lg-5" style={{ lineHeight: '1.6' }}>
                  
                </div>

                <div class="col-lg-3 col-6 col-md-6">
                    <h6 class="text-uppercase font-monospace text-white fw-bold mb-3 small" style={{ letterSpacing: '1px' }}>System Tree</h6>
                    <ul class="list-unstyled mb-0 d-grid gap-2">
                        <li><a href="index.php" class="text-decoration-none small footer-link"><i class="fa-solid fa-chevron-right me-2 opacity-50" style={{ fontSize: '0.7rem' }}></i>Home Showcase</a></li>
                        <li><a href="all-books.php" class="text-decoration-none small footer-link"><i class="fa-solid fa-chevron-right me-2 opacity-50" style={{ fontSize: '0.7rem' }}></i>Global Catalog</a></li>
                        <li><a href="signup.php" class="text-decoration-none small footer-link"><i class="fa-solid fa-chevron-right me-2 opacity-50" style={{ fontSize: '0.7rem' }}></i>Client Registration</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-6 col-md-6">
                    <h6 class="text-uppercase font-monospace text-white fw-bold mb-3 small" style={{ letterSpacing: '1px' }}>Gateways</h6>
                    <ul class="list-unstyled mb-0 d-grid gap-2">
                        <li><a href="login.php" class="text-decoration-none small footer-link"><i class="fa-solid fa-key me-2 opacity-50" style={{ fontSize: '0.7rem' }}></i>Client Portal Terminal</a></li>
                        <li><a href="http://localhost/book-shop-system/admin/" class="text-decoration-none small footer-link"><i class="fa-solid fa-shield-halved me-2 opacity-50" style={{ fontSize: '0.7rem' }}></i>Secure Executive Subsystem</a></li>
                    </ul>
                </div>
            </div>

            <hr class="border-secondary border-opacity-20 my-4">

      
            <div class="row align-items-center">
                <div class="col-md-12 text-center text-md-start">
                    <p class="text-white-50 mb-0 small text-center">
                        &copy; <?= date('Y') ?> <span class="custom-text-yellow fw-semibold">BookHeaven</span> Digital Ecosystem Framework. Built with PHP & Bootstrap 5.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>