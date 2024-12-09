<?php
session_start();
require_once 'core/dbConfig.php';

if (!isset($_SESSION['hr_id'])) {
    header('Location: login.php');
    exit();
}

$hr_id = $_SESSION['hr_id'];

$stmt = $pdo->prepare("SELECT a.id AS applicant_id, a.username, a.email, j.title AS job_title, j.company AS company_name 
                       FROM applications app 
                       JOIN applicants a ON app.applicant_id = a.id 
                       JOIN job_posts j ON app.job_id = j.id 
                       WHERE app.status = 'Accepted' AND j.posted_by = :hr_id");
$stmt->bindParam(':hr_id', $hr_id);
$stmt->execute();
$acceptedApplicants = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accepted Applicants</title>
    <style>
        .container { width: 80%; margin: 0 auto; }
        .applicant { border: 1px solid #ddd; padding: 10px; margin: 10px 0; }
        .applicant p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1>Accepted Applicants</h1>

        <?php if (empty($acceptedApplicants)): ?>
            <p>No accepted applicants found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Applicant Name</th>
                        <th>Email</th>
                        <th>Job Title</th>
                        <th>Company Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($acceptedApplicants as $applicant): ?>
                        <tr>
                            <td><?= htmlspecialchars($applicant['username']) ?></td>
                            <td><?= htmlspecialchars($applicant['email']) ?></td>
                            <td><?= htmlspecialchars($applicant['job_title']) ?></td>
                            <td><?= htmlspecialchars($applicant['company_name']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
