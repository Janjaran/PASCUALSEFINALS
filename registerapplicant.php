<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: applicants.php');  
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
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
            max-width: 550px;
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
            gap: 2px;
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

        input[type="submit"], button[type="submit"] {
            padding: 12px;
            font-size: 16px;
            color: white;
            background-color: rgb(88, 75, 57);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover, button[type="submit"]:hover {
            background-color: rgb(88, 75, 57);
        }

        @media (max-width: 600px) {
            .form-container {
                padding: 15px;
                max-width: 90%;
            }
        }
    </style>
    <title>Register as Applicant</title>
</head>
<body>
    <div class="form-container">
        <h2>Register as Applicant</h2>
        <form action="core/handleForms.php" method="POST">
            <label for="username">Username</label><br>
            <input type="text" name="username" required><br><br>

            <label for="email">Email</label><br>
            <input type="email" name="email" required><br><br>

            <label for="password">Password</label><br>
            <input type="password" name="password" required><br><br>

            <label for="confirm_password">Confirm Password</label><br>
            <input type="password" name="confirm_password" required><br><br>

            <button type="submit" name="registerApplicantBtn">Register</button>
        </form>
    </div>
</body>
</html>
