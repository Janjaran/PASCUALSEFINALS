<?php
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 

session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$sender_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['message']) && isset($_POST['hr_id'])) {
        $recipient_id = $_POST['hr_id'];
        $message = trim($_POST['message']);  

        if (!empty($message)) {
            $stmt = $pdo->prepare("INSERT INTO messages (sender_id, recipient_id, message) VALUES (?, ?, ?)");
            $stmt->execute([$sender_id, $recipient_id, $message]);

            header("Location: messages.php"); 
            exit();
        } else {
            $error = "Message cannot be empty!";
        }
    } else {
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Message</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <?php include 'navbarapplicant.php'; ?>

    <h1>Send a Message</h1>

    <?php if (isset($error)): ?>
        <div style="color: red;"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="send-message.php" method="POST">
        <label for="hr_id">Select HR User</label>
        <select name="hr_id" required>
            <?php
            $stmt = $pdo->prepare("SELECT * FROM hr_users");
            $stmt->execute();
            $hr_users = $stmt->fetchAll();

            foreach ($hr_users as $hr): ?>
                <option value="<?php echo $hr['id']; ?>"><?php echo $hr['username']; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="message">Message</label><br>
        <textarea name="message" required placeholder="Write your message..."></textarea><br><br>

        <input type="submit" value="Send Message">
    </form>
</body>
</html>
