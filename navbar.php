<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
<div class="navbar">
    <img src="findhire.png" alt="Logo" width="150" height="150">
    <h1>Welcome to FindHire, <span style="color: #FFD700;"><?php echo isset($_SESSION['user']) ? $_SESSION['user'] : 'Guest'; ?></span></h1>
    <h3>
        <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Home</a>
        <a href="accepted_applicants.php" class="<?= basename($_SERVER['PHP_SELF']) == 'accepted_applicants.php' ? 'active' : '' ?>">Accepted Applicants</a>
        <a href="viewmessages.php" class="<?= basename($_SERVER['PHP_SELF']) == 'viewmessages.php' ? 'active' : '' ?>">Messages</a>
        <a href="logout.php">Logout</a>
    </h3>
</div>
</body>
</html>
