<?php
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 

session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$sender_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM messages WHERE sender_id = ? OR recipient_id = ?");
$stmt->execute([$sender_id, $sender_id]);
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
    <?php include 'navbarapplicant.php'; ?>

    <h1>Your Messages</h1>

    <?php foreach ($messages as $message): ?>
        <div class="message">
            <p><strong>Sender ID:</strong> <?php echo $message['sender_id']; ?></p>
            <p><strong>Message:</strong> <?php echo $message['message']; ?></p>
            <p><strong>Sent At:</strong> <?php echo $message['sent_at']; ?></p>
        </div>
    <?php endforeach; ?>
</body>
</html>
