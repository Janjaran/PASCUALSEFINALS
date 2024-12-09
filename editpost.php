<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';
require_once 'core/handleForms.php';

// Check if the HR user is logged in
if (!isset($_SESSION['hr_id'])) {
    header('Location: login.php');
    exit();
}

// Check if the job ID is provided in the URL
if (isset($_GET['job_id'])) {
    $job_id = $_GET['job_id'];

    // Fetch the job details from the database
    $job = getJobPostById($pdo, $job_id);

    // Check if the job exists and belongs to the logged-in HR user
    if (!$job || $job['posted_by'] != $_SESSION['hr_id']) {
        echo "Job not found or you don't have permission to edit this post.";
        exit();
    }
} else {
    echo "No job ID provided.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['jobTitle'];
    $description = $_POST['jobDescription'];
    $company = $_POST['company'];
    $location = $_POST['location'];
    $salary = $_POST['salary'];

    $stmt = $pdo->prepare("UPDATE job_posts SET title = :title, description = :description, company = :company, location = :location, salary = :salary WHERE id = :job_id");
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':company', $company);
    $stmt->bindParam(':location', $location);
    $stmt->bindParam(':salary', $salary);
    $stmt->bindParam(':job_id', $job_id, PDO::PARAM_INT);
    $stmt->execute();

    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles/styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Job Post</title>
</head>
<body>
    <h1>Edit Job Post</h1>

    <form action="editpost.php?job_id=<?= $job_id ?>" method="POST">
        <label>Job Title:</label><br>
        <input type="text" name="jobTitle" value="<?= htmlspecialchars($job['title']) ?>" required><br>

        <label>Description:</label><br>
        <textarea name="jobDescription" required><?= htmlspecialchars($job['description']) ?></textarea><br>

        <label>Company Name:</label><br>
        <input type="text" name="company" value="<?= htmlspecialchars($job['company']) ?>" required><br>

        <label>Location:</label><br>
        <input type="text" name="location" value="<?= htmlspecialchars($job['location']) ?>"><br>

        <label>Salary:</label><br>
        <input type="number" step="0.01" name="salary" value="<?= htmlspecialchars($job['salary']) ?>"><br>

        <button type="submit">Update Job Post</button>
    </form>
</body>
</html>
