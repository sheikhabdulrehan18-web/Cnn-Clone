<?php
require_once 'db.php';
 
$error = '';
 
// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
 
    if (empty($email) || empty($password)) {
        $error = 'Email and password are required.';
    } else {
        // Get user from database
        $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
 
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
 
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
 
                // Redirect to homepage
                header('Location: index.php');
                exit();
            } else {
                $error = 'Invalid email or password.';
            }
        } else {
            $error = 'Invalid email or password.';
        }
 
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CNN Clone</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="nav-container">
            <a href="index.php" class="logo">CNN CLONE</a>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="signup.php">Sign Up</a></li>
                </ul>
            </nav>
        </div>
    </header>
 
    <div class="form-container">
        <h2 class="form-title">Login</h2>
 
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
 
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required 
                       value="<?php echo htmlspecialchars($email ?? ''); ?>">
            </div>
 
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
 
            <button type="submit" class="btn">Login</button>
        </form>
 
        <p style="text-align: center; margin-top: 20px;">
            Don't have an account? <a href="signup.php" style="color: var(--cnn-red);">Sign up here</a>
        </p>
    </div>
 
    <footer>
        <p>&copy; 2024 CNN Clone. All rights reserved.</p>
    </footer>
 
    <script src="script.js"></script>
</body>
</html>
 
 
