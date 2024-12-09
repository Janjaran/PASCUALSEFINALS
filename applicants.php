<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

require_once 'core/dbConfig.php';

$stmt = $pdo->prepare("SELECT * FROM job_posts");
$stmt->execute();
$job_posts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles/styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Dashboard</title>
</head>
<body>
<?php include 'navbarapplicant.php'; ?>

    <div class="container">
        <h3>Available Job Posts</h3>

        <div class="job-container">
            <?php foreach ($job_posts as $job): ?>
                <div class="job-post">
                    <h4><?php echo $job['title']; ?></h4>

                    <div class="job-details">
                        <p><strong>Company:</strong> <?php echo $job['company']; ?></p>
                        <p><strong>Location:</strong> <?php echo $job['location']; ?></p>
                        <p><strong>Description:</strong> <?php echo $job['description']; ?></p>
                        <p><strong>Salary:</strong> $<?php echo $job['salary']; ?></p>
                    </div>

                    <div class="job-actions">
                        <form action="send-message.php" method="POST">
                            <input type="hidden" name="hr_id" value="<?php echo $job['posted_by']; ?>"> 
                            <button type="submit">Send Message</button>
                        </form>
                        <form action="apply.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">

                            <label for="cover_letter">Cover Letter:</label><br>
                            <textarea name="cover_letter" required placeholder="Write your cover letter here..."></textarea><br><br>

                            <label for="resume">Upload Resume (PDF):</label><br>
                            <input type="file" name="resume" accept=".pdf" required><br><br>

                            <button type="submit" name="applyBtn">Apply</button>
                        </form>
                    </div>

                    <hr>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</body>
</html>
