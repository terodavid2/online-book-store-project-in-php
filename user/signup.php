<?php
include '../config/db.php';
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
    
    if ($check->num_rows > 0) {
        $message = "Email is already registered!";
    } else {
        $sql = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', 'user')";
        if ($conn->query($sql)) {
            header("Location: login.php?success=1");
            exit();
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SignUp | BookHeaven</title>
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
        <h2 class="text-center mb-4 fw-bold">Create Account</h2>
        <?php if($message): ?>
            <div class="alert alert-danger py-2 small"><?= $message ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="small text-secondary">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="small text-secondary">Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="small text-secondary">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2 mt-3 fw-bold">SignUp</button>
        </form>
        <p class="text-center mt-4 small text-secondary">Already have an account? <a href="login.php" class="text-primary text-decoration-none">LogIn</a></p>
    </div>
</body>
</html>