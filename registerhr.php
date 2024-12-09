<?php  
require_once 'core/models.php';  
require_once 'core/handleForms.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles/styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        h1, h2, h3 {
            color: #333;
        }

        .form-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .form-title {
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }

        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
        }

        input[type="submit"] {
            padding: 12px;
            font-size: 16px;
            color: white;
            background-color: rgb(88, 75, 57);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        h2.message {
            text-align: center;
            color: green;
        }

        h2.message-error {
            text-align: center;
            color: red;
        }

        @media (max-width: 600px) {
            .form-container {
                padding: 15px;
                max-width: 90%;
            }
        }
    </style>
    <title>Register HR</title>
</head>
<body>
    <div class="form-container">
        <h1>Register an HR!</h1>
        <?php  
        if (isset($_SESSION['message']) && isset($_SESSION['status'])) {
            if ($_SESSION['status'] == "200") {
                echo "<h2 style='color: green;'>{$_SESSION['message']}</h2>";
            } else {
                echo "<h2 style='color: red;'>{$_SESSION['message']}</h2>";    
            }
            unset($_SESSION['message']);
            unset($_SESSION['status']);
        }
        ?>

        <form action="core/handleForms.php" method="POST">
            <p>
                <label for="username">Username</label>
                <input type="text" name="username" required>
            </p>
            <p>
                <label for="email">Email</label>
                <input type="email" name="email" required>
            </p>
            <p>
                <label for="password">Password</label>
                <input type="password" name="password" required>
            </p>
            <p>
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" required>
            </p>
            <p>
                <input type="submit" name="registerHRBtn" value="Register">
            </p>
        </form>
    </div>
</body>
</html>
