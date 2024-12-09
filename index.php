<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';
require_once 'core/handleForms.php';

// Check if the HR user is logged in
if (!isset($_SESSION['hr_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch all job posts created by HR for display
$jobPosts = getAllJobPosts($pdo, $_SESSION['hr_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles/styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Dashboard</title>
</head>
<body>
<?php include 'navbar.php'; ?>
    <div class="container">
        <h1>HR Dashboard</h1>
        
        <form action="core/handleForms.php" method="POST">
            <h3>Create a Job Post</h3>
            <label>Job Title:</label><br>
            <input type="text" name="jobTitle" required><br>

            <label>Description:</label><br>
            <textarea name="jobDescription" required></textarea><br>

            <label>Company Name:</label><br>
            <input type="text" name="company" required><br>

            <label>Location:</label><br>
            <input type="text" name="location"><br>

            <label>Salary:</label><br>
            <input type="number" step="0.01" name="salary"><br>

            <button type="submit" name="postJobBtn">Post Job</button>
        </form>

        <h2>Your Job Posts</h2>

        <?php if (empty($jobPosts)): ?>
            <p>You have not posted any jobs yet.</p>
        <?php else: ?>
            <?php foreach ($jobPosts as $job): ?>
                <div class="job-post">
                    <div class="job-header">
                        <strong><?= htmlspecialchars($job['title']) ?></strong>
                        <span style="font-size: 12px; color: #777;"><?= htmlspecialchars($job['created_at']) ?></span>
                    </div>
                    <div class="job-details">
                        <p><strong>Company:</strong> <?= htmlspecialchars($job['company']) ?></p>
                        <p><strong>Location:</strong> <?= htmlspecialchars($job['location']) ?></p>
                        <p><strong>Salary:</strong> $<?= htmlspecialchars($job['salary']) ?></p>
                        <p><strong>Description:</strong></p>
                        <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
                    </div>
                    <div class="job-actions">
                        <form action="core/handleForms.php" method="POST">
                            <input type="hidden" name="job_id" value="<?= htmlspecialchars($job['id']) ?>">
                            <button type="submit" name="viewApplicantsBtn">View Applicants</button>
                        </form>

                        <form action="editpost.php" method="GET" style="display:inline;">
                            <input type="hidden" name="job_id" value="<?= htmlspecialchars($job['id']) ?>">
                            <button type="submit" name="editJobBtn">Edit</button>
                        </form>

                        <form action="deletepost.php" method="GET" style="display:inline;">
                            <input type="hidden" name="job_id" value="<?= htmlspecialchars($job['id']) ?>">
                            <button type="submit" name="deleteJobBtn">Delete</button>
                        </form>
                    </div>
                </div>
                <hr>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
