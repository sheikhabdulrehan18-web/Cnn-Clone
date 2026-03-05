<?php
require_once 'db.php';
 
$error = '';
$success = '';
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
 
    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        // Check if email already exists
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
 
        if ($result->num_rows > 0) {
            $error = 'Email already registered. Please login.';
            $check_stmt->close();
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
 
            // Insert user
            $insert_stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $insert_stmt->bind_param("sss", $name, $email, $hashed_password);
 
            if ($insert_stmt->execute()) {
                $success = 'Registration successful! You can now <a href="login.php">login</a>.';
                // Clear form
                $name = '';
                $email = '';
            } else {
                $error = 'Registration failed. Please try again.';
            }
 
            $insert_stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - CNN Clone</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="nav-container">
            <a href="index.php" class="logo">CNN CLONE</a>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="login.php">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>
 
    <div class="form-container">
        <h2 class="form-title">Sign Up</h2>
 
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
 
        <?php if ($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
 
        <form method="POST" action="signup.php">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required 
                       value="<?php echo htmlspecialchars($name ?? ''); ?>">
            </div>
 
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required 
                       value="<?php echo htmlspecialchars($email ?? ''); ?>">
            </div>
 
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required 
                       minlength="6">
            </div>
 
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required 
                       minlength="6">
            </div>
 
            <button type="submit" class="btn">Sign Up</button>
        </form>
 
        <p style="text-align: center; margin-top: 20px;">
            Already have an account? <a href="login.php" style="color: var(--cnn-red);">Login here</a>
        </p>
    </div>
 
    <footer>
        <p>&copy; 2024 CNN Clone. All rights reserved.</p>
    </footer>
 
    <script src="script.js"></script>
</body>
</html>
 
 
