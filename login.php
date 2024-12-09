<?php
session_start();
require_once 'core/dbConfig.php';

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'hr') {
        header('Location: index.php');
        exit();
    } else {
        header('Location: applicants.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM applicants WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        $stmt = $pdo->prepare("SELECT * FROM hr_users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
    }

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user['username']; 
        $_SESSION['user_id'] = $user['id'];
        if ($user['role'] == 'hr') {
            $_SESSION['hr_id'] = $user['id']; 
            $_SESSION['role'] = 'hr'; 
            header('Location: index.php'); 
        } else {
            $_SESSION['role'] = 'applicant'; 
            header('Location: applicants.php'); 
        }
        exit();
    } else {
        $_SESSION['message'] = 'Invalid email or password.';
        header('Location: login.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles/styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>

<div class="login-container">
    <img src="findhire.png" alt="Logo" width="150" height="150">
    <h1>Welcome to Findhire!</h1>
    <?php if (isset($_SESSION['message'])): ?>
        <p class="error-message"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <button type="submit">Login</button>
    </form>

    <hr>

    <form action="registerhr.php" method="GET">
        <button type="submit">Register as HR</button>
    </form>

    <form action="registerapplicant.php" method="GET">
        <button type="submit">Register as Applicant</button>
    </form>
</div>

</body>
</html>
