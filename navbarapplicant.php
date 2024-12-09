<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <style>
        .navbar {
            background-color: #333;
            overflow: hidden;
            font-family: Arial, sans-serif;
        }
        
        .navbar img {
            display: inline-block;
            vertical-align: middle;
            margin-left: 20px;
            height: 50px;
        }
        
        .navbar h1 {
            display: inline-block;
            color: #fff;
            font-size: 24px;
            margin-left: 10px;
            vertical-align: middle;
        }

        .navbar h3 {
            float: right;
            margin-right: 20px;
            padding: 15px 20px;
        }

        .navbar a {
            text-decoration: none;
            color: #fff;
            padding: 12px 20px;
            margin: 0 10px;
            background-color: rgb(88, 75, 57);
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .navbar a:hover {
            background-color: #45a049;
        }

        .navbar a.active {
            background-color: #333;
        }
    </style>
</head>
<body>
<div class="navbar">
    <img src="findhire.png" alt="Logo" width="50" height="50">
    <h1>Welcome to FindHire, <span style="color: #FFD700;"><?php echo isset($_SESSION['user']) ? $_SESSION['user'] : 'Guest'; ?></span></h1>
    <h3>
        <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Home</a>
        <a href="messages.php">Messages</a>
        <a href="logout.php">Logout</a>
    </h3>
</div>

</body>
</html>
