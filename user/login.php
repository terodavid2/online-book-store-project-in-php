<?php
include '../config/db.php';
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Role-based redirection
            if ($user['role'] == 'admin') {
                // Redirect to the React Admin port (Default Vite port is 5173)
                header("Location: http://localhost:5173");
            } else {
                header("Location: profile.php");
            }
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LogIn | BookHeaven</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #1a1a1a; color: white; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .auth-card { background: #252525; padding: 40px; border-radius: 12px; width: 100%; max-width: 400px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .form-control { background: #333; border: 1px solid #444; color: white; }
        .form-control:focus { background: #333; color: white; border-color: #0d6efd; box-shadow: none; }
    </style>
</head>
<body>
    <div class="auth-card">
        <h2 class="text-center mb-4 fw-bold">Welcome Back</h2>
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success py-2 small">Registration successful! Please login.</div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="alert alert-danger py-2 small"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="small text-secondary">Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="small text-secondary">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2 mt-3 fw-bold">LogIn</button>
        </form>
        <p class="text-center mt-4 small text-secondary">Don't have an account? <a href="signup.php" class="text-primary text-decoration-none">SignUp</a></p>
    </div>
</body>
</html>