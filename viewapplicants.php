<?php
session_start();
require_once 'core/dbConfig.php';
require_once 'core/models.php';

if (!isset($_SESSION['hr_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['job_id']) && is_numeric($_GET['job_id'])) {
    $jobId = $_GET['job_id'];

    $applicants = getApplicantsForJob($pdo, $jobId);
} else {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $application_id = $_POST['application_id'];
    $action = $_POST['action'];

    if ($action == 'accept') {
        $status = 'Accepted';
    } elseif ($action == 'reject') {
        $status = 'Rejected';
    }

    $stmt = $pdo->prepare("UPDATE applications SET status = :status WHERE id = :application_id");
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':application_id', $application_id);
    $stmt->execute();

    header("Location: viewapplicants.php?job_id=$jobId");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicants for Job</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 80%;
            margin: 0 auto;
        }
        .applicant {
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px 0;
        }
        .applicant p {
            margin: 5px 0;
        }
        .download-link {
            color: #007bff;
            text-decoration: none;
        }
        .download-link:hover {
            text-decoration: underline;
        }
        .actions {
            margin-top: 10px;
        }
        .actions button {
            padding: 8px 15px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
        }
        .accept {
            background-color: #28a745;
            color: white;
        }
        .accept:hover {
            background-color: #218838;
        }
        .reject {
            background-color: #dc3545;
            color: white;
        }
        .reject:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
    <div class="container">
        <h1>Applicants for Job #<?= htmlspecialchars($jobId) ?></h1>

        <?php if (empty($applicants)): ?>
            <p>No applicants for this job yet.</p>
        <?php else: ?>
            <?php foreach ($applicants as $applicant): ?>
                <div class="applicant">
                    <p><strong>Name:</strong> <?= htmlspecialchars($applicant['username']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($applicant['email']) ?></p>

                    <p><strong>Cover Letter:</strong></p>
                    <p><?= nl2br(htmlspecialchars($applicant['cover_letter'])) ?></p>

                    <?php
                    $stmt = $pdo->prepare("SELECT file_path FROM pdf_uploads WHERE application_id = :application_id");
                    $stmt->bindParam(':application_id', $applicant['application_id']);
                    $stmt->execute();
                    $pdf = $stmt->fetch();
                    ?>

                    <p><strong>Resume:</strong> 
                    <?php if ($pdf && file_exists($pdf['file_path'])): ?>
                        <a href="<?= $pdf['file_path'] ?>" class="download-link" download>Download Resume</a>
                    <?php else: ?>
                        <span>No resume available for download.</span>
                    <?php endif; ?>
                    </p>

                    <div class="actions">
                        <?php if (isset($applicant['status']) && $applicant['status'] != 'Accepted' && $applicant['status'] != 'Rejected'): ?>
                            <form method="POST" action="">
                                <input type="hidden" name="application_id" value="<?= htmlspecialchars($applicant['application_id']) ?>">
                                <button type="submit" name="action" value="accept" class="accept">Accept</button>
                                <button type="submit" name="action" value="reject" class="reject">Reject</button>
                            </form>
                        <?php else: ?>
                            <p>Status: <?= htmlspecialchars($applicant['status']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
