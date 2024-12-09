<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';
require_once 'core/handleForms.php';

if (!isset($_SESSION['hr_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['job_id'])) {
    $job_id = $_GET['job_id'];

    $job = getJobPostById($pdo, $job_id);

    if ($job && $job['posted_by'] == $_SESSION['hr_id']) {
        if (isset($_POST['confirmDelete'])) {
            deleteJobPost($pdo, $job_id);
            header('Location: index.php'); 
            exit();
        }
    } else {
        echo "Job not found or you don't have permission to delete this post.";
        exit();
    }
} else {
    echo "No job ID provided.";
    exit();
}

function deleteJobPost($pdo, $job_id) {
    $stmt = $pdo->prepare("DELETE FROM job_posts WHERE id = :job_id");
    $stmt->bindParam(':job_id', $job_id, PDO::PARAM_INT);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles/styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Job Post</title>
</head>
<body>
    <h1>Delete Job Post</h1>

    <p>Are you sure you want to delete the following job post?</p>

    <h3>Job Title: <?= htmlspecialchars($job['title']) ?></h3>
    <p><strong>Company:</strong> <?= htmlspecialchars($job['company']) ?></p>
    <p><strong>Location:</strong> <?= htmlspecialchars($job['location']) ?></p>
    <p><strong>Salary:</strong> $<?= htmlspecialchars($job['salary']) ?></p>

    <form action="deletepost.php?job_id=<?= $job_id ?>" method="POST">
        <button type="submit" name="confirmDelete">Yes, Delete</button>
        <a href="index.php"><button type="button">Cancel</button></a>
    </form>
</body>
</html>
