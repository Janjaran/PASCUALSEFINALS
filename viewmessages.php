<?php
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 

session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$hr_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM messages WHERE recipient_id = ? ORDER BY sent_at DESC");
$stmt->execute([$hr_id]);
$messages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <h1>Received Messages</h1>

    <?php foreach ($messages as $message): ?>
        <div class="message" style="background-color: lightgrey; margin: 20px; padding: 10px;">
            <h4>From Applicant (ID: <?php echo $message['sender_id']; ?>)</h4>
            <p><strong>Message:</strong> <?php echo $message['message']; ?></p>
            <p><strong>Sent at:</strong> <?php echo $message['sent_at']; ?></p>
            
            <form action="reply-message.php" method="POST">
                <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                <textarea name="reply" placeholder="Write your reply here..." required></textarea><br><br>
                <input type="submit" name="send_reply" value="Send Reply">
            </form>
        </div>
    <?php endforeach; ?>
</body>
</html>
