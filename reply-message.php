<?php
require_once 'core/dbConfig.php'; 

session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$hr_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message_id'], $_POST['reply'])) {
    $message_id = $_POST['message_id'];
    $reply = trim($_POST['reply']);
    
    if (!empty($reply)) {
        // Get the sender_id (applicant) of the original message
        $stmt = $pdo->prepare("SELECT sender_id FROM messages WHERE id = ?");
        $stmt->execute([$message_id]);
        $message = $stmt->fetch();

        if ($message) {
            $sender_id = $message['sender_id']; // Get the original sender_id (applicant)

            // Insert the reply as a new message in the messages table
            $stmt = $pdo->prepare("INSERT INTO messages (sender_id, recipient_id, message) VALUES (?, ?, ?)");
            $stmt->execute([$hr_id, $sender_id, $reply]);

            header("Location: messages.php"); // Redirect back to messages page
            exit();
        }
    } else {
        $error = "Reply cannot be empty!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reply to Message</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <?php include 'navbarhr.php'; ?>

    <h1>Reply to Message</h1>

    <?php if (isset($error)): ?>
        <div style="color: red;"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="reply-message.php" method="POST">
        <label for="reply">Your Reply</label><br>
        <textarea name="reply" placeholder="Write your reply here..." required></textarea><br><br>
        <input type="hidden" name="message_id" value="<?php echo $_GET['message_id']; ?>">
        <input type="submit" name="send_reply" value="Send Reply">
    </form>
</body>
</html>
